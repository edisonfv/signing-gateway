<?php

namespace App\Signing\Health;

use App\Enums\Environment;
use App\Models\ApiClient;
use App\Models\ClientNotification;
use App\Models\Heartbeat;
use App\Models\PersonalAccessToken;
use App\Models\ProviderAccount;
use App\Security\TlsTrust;
use App\Signing\ApiFlow\TrackingBudget;
use App\Signing\EmissionGuard;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Estado de salud del gateway para el monitor de la plataforma (GET /v1/platform/health) y para
 * gateway:doctor. Solo métricas y fechas, nunca datos de titulares.
 *
 * Cada comprobación es ok, warn o fail; el estado general es down si alguna falla, degraded si
 * alguna avisa y ok si no.
 */
class GatewayHealth
{
    /** Colas que atienden los workers, en orden de prioridad. */
    public const QUEUES = ['interactive', 'default', 'notifications'];

    /** Minutos sin señal del scheduler o de una cola para darlos por caídos. */
    private const STALE_MINUTES = 3;

    public function __construct(
        private TrackingBudget $budget,
        private EmissionGuard $emissionGuard,
        private TlsTrust $tls,
    ) {}

    /**
     * @return array{status: string, checked_at: string, checks: array<int, array{key: string, label: string, status: string, detail: string, hint: string|null}>}
     */
    public function report(): array
    {
        $checks = [
            $this->scheduler(),
            ...array_map(fn (string $queue) => $this->queue($queue), self::QUEUES),
            $this->failedJobs(),
            $this->notifications(),
            ...$this->providers(),
            $this->reconciliation(),
            $this->emission(),
            $this->platformCredential(),
            ...$this->tls(),
            $this->ledger(),
        ];

        $statuses = array_column($checks, 'status');

        return [
            'status' => in_array('fail', $statuses, true) ? 'down' : (in_array('warn', $statuses, true) ? 'degraded' : 'ok'),
            'checked_at' => now()->toIso8601String(),
            'checks' => $checks,
        ];
    }

    private function scheduler(): array
    {
        $at = Heartbeat::lastAt('scheduler');

        return $at !== null && $at->gt(now()->subMinutes(self::STALE_MINUTES))
            ? $this->ok('scheduler', 'Scheduler', 'Última ejecución '.$this->ago($at))
            : $this->fail('scheduler', 'Scheduler', $at ? 'Sin ejecutarse desde '.$this->ago($at) : 'Nunca se ha ejecutado', 'Revise el servicio gateway-scheduler (php artisan schedule:work).');
    }

    private function queue(string $queue): array
    {
        $beat = Heartbeat::query()->find("queue:{$queue}");
        $pending = DB::table('jobs')->where('queue', $queue)->whereNull('reserved_at');
        $count = (clone $pending)->count();
        $oldest = (clone $pending)->min('available_at');
        $oldestWait = $oldest ? max(0, now()->getTimestamp() - (int) $oldest) : 0;
        $detail = "{$count} en espera".($oldest ? ", la más antigua hace {$oldestWait} s" : '');
        $label = "Cola {$queue}";

        if ($beat === null || $beat->beat_at->lte(now()->subMinutes(self::STALE_MINUTES))) {
            return $this->fail("queue:{$queue}", $label, ($beat ? 'Sin procesar desde '.$this->ago($beat->beat_at) : 'Nunca se ha procesado').'; '.$detail, "Revise el worker que atiende «{$queue}» (php artisan queue:work --queue={$queue}).");
        }

        // La cola interactive es la del titular en línea: un minuto de atraso ya se nota.
        $warnAfter = $queue === 'interactive' ? 60 : 600;

        return $oldestWait > $warnAfter
            ? $this->warn("queue:{$queue}", $label, "Atrasada: {$detail}", 'Hay más trabajo que workers: agregue un worker para esta cola.')
            : $this->ok("queue:{$queue}", $label, 'Procesando; '.$detail);
    }

    private function failedJobs(): array
    {
        $lastDay = DB::table('failed_jobs')->where('failed_at', '>=', now()->subDay());
        $total = (clone $lastDay)->count();
        $interactive = (clone $lastDay)->where('queue', 'interactive')->where('failed_at', '>=', now()->subHour())->count();

        return match (true) {
            $interactive > 0 => $this->fail('failed-jobs', 'Jobs fallidos', "{$interactive} de la cola interactive en la última hora ({$total} en 24 h)", 'Revise php artisan queue:failed antes de reintentar: una emisión no debe pedirse dos veces.'),
            $total > 0 => $this->warn('failed-jobs', 'Jobs fallidos', "{$total} en las últimas 24 h", 'Revise php artisan queue:failed.'),
            default => $this->ok('failed-jobs', 'Jobs fallidos', 'Ninguno en las últimas 24 h'),
        };
    }

    private function notifications(): array
    {
        $failed = ClientNotification::query()->where('failed_at', '>=', now()->subHour())->count();
        $stuck = ClientNotification::query()->whereNull('delivered_at')->whereNull('failed_at')->where('created_at', '<=', now()->subMinutes(15))->count();

        return match (true) {
            $failed > 0 => $this->warn('notifications', 'Notificaciones a la plataforma', "{$failed} fallidas en la última hora", 'Revise que la URL de notificaciones de la plataforma responda (dominio de GATEWAY_NOTIFICATION_HOSTS).'),
            $stuck > 0 => $this->warn('notifications', 'Notificaciones a la plataforma', "{$stuck} pendientes hace más de 15 minutos", 'La plataforma no está aceptando los avisos: se siguen reintentando.'),
            default => $this->ok('notifications', 'Notificaciones a la plataforma', 'Al día'),
        };
    }

    /**
     * @return array<int, array{key: string, label: string, status: string, detail: string, hint: string|null}>
     */
    private function providers(): array
    {
        $accounts = ProviderAccount::query()
            ->where('environment', Environment::from(config('gateway.environment'))->value)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        if ($accounts->isEmpty()) {
            return [$this->fail('provider', 'Proveedor', 'No hay ninguna cuenta activa en este ambiente', 'php artisan gateway:provider:credentials '.config('gateway.environment'))];
        }

        return $accounts->flatMap(fn (ProviderAccount $account) => [
            $this->provider($account),
            $this->trackingBudget($account),
        ])->all();
    }

    private function provider(ProviderAccount $account): array
    {
        $ok = Heartbeat::query()->find("provider:{$account->getKey()}:ok");
        $error = Heartbeat::query()->find("provider:{$account->getKey()}:error");
        $key = "provider:{$account->getKey()}";
        $label = "Proveedor {$account->name}";

        if ($error !== null && ($ok === null || $error->beat_at->gt($ok->beat_at))) {
            $what = $error->meta['status'] ? "HTTP {$error->meta['status']}" : ($error->meta['message'] ?? 'sin conexión');
            $recent = $error->beat_at->gt(now()->subMinutes(15));

            return ($recent ? $this->fail(...) : $this->warn(...))($key, $label, "Último error {$this->ago($error->beat_at)}: {$what}", '¿Token vigente? ¿La IP del servidor sigue autorizada en el proveedor? Pruebe con php artisan gateway:doctor.');
        }

        return $ok !== null
            ? $this->ok($key, $label, 'Responde; última llamada correcta '.$this->ago($ok->beat_at))
            : $this->ok($key, $label, 'Sin llamadas todavía');
    }

    private function trackingBudget(ProviderAccount $account): array
    {
        $limit = $this->budget->limit();
        $used = $limit - $this->budget->remaining($account);
        $percent = $limit > 0 ? (int) round($used * 100 / $limit) : 0;
        $key = "tracking-budget:{$account->getKey()}";
        $label = "Consultas de estado ({$account->name})";
        $detail = "{$used} de {$limit} en la última hora ({$percent} %)";

        return match (true) {
            $used >= $limit => $this->fail($key, $label, "Agotado: {$detail}", 'Las solicitudes se reconcilian más lento hasta la próxima hora.'),
            $percent >= 80 => $this->warn($key, $label, $detail, 'Hay muchas solicitudes abiertas: si se mantiene, pida al proveedor más cupo.'),
            default => $this->ok($key, $label, $detail),
        };
    }

    private function reconciliation(): array
    {
        $beat = Heartbeat::query()->find('reconcile');

        return match (true) {
            $beat === null => $this->warn('reconcile', 'Reconciliación', 'Aún no se ha ejecutado', 'La ejecuta el scheduler cada 5 minutos.'),
            $beat->beat_at->lte(now()->subMinutes(15)) => $this->fail('reconcile', 'Reconciliación', 'Sin ejecutarse desde '.$this->ago($beat->beat_at), 'Revise el scheduler y php artisan gateway:reconcile-sessions.'),
            default => $this->ok('reconcile', 'Reconciliación', 'Última '.$this->ago($beat->beat_at).'; encoló '.($beat->meta['queued'] ?? 0)),
        };
    }

    private function emission(): array
    {
        $halt = $this->emissionGuard->haltDetails();

        return $halt === null
            ? $this->ok('emission', 'Emisión', 'Activa')
            : $this->warn('emission', 'Emisión', "DETENIDA desde {$halt['at']}: {$halt['reason']}", 'Reanudar con php artisan gateway:halt --off.');
    }

    private function platformCredential(): array
    {
        $token = PersonalAccessToken::query()
            ->whereHasMorph('tokenable', [ApiClient::class], fn ($client) => $client->whereNull('tenant_id')->whereNull('revoked_at'))
            ->where('abilities', 'like', '%clients.manage%')
            ->where(fn ($expiry) => $expiry->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByRaw('expires_at IS NULL DESC')
            ->orderByDesc('expires_at')
            ->first();

        if ($token === null) {
            return $this->fail('platform-credential', 'Credencial de alta de la plataforma', 'Sin credencial vigente', 'Vuelva a emparejar: php artisan gateway:pair.');
        }

        if ($token->expires_at === null) {
            return $this->ok('platform-credential', 'Credencial de alta de la plataforma', 'Vigente, sin vencimiento');
        }

        $days = (int) now()->diffInDays($token->expires_at);

        return $days < 30
            ? $this->warn('platform-credential', 'Credencial de alta de la plataforma', "Vence en {$days} días ({$token->expires_at->toDateString()})", 'La plataforma debe renovarla: POST /v1/admin/provisioning-credential.')
            : $this->ok('platform-credential', 'Credencial de alta de la plataforma', "Vence el {$token->expires_at->toDateString()}");
    }

    /**
     * Vencimiento del certificado de la API y del cambio al siguiente (deploy/tls-rotate.sh). Sin
     * comprobación si el gateway no administra su certificado.
     *
     * @return array<int, array{key: string, label: string, status: string, detail: string, hint: string|null}>
     */
    private function tls(): array
    {
        if (! $this->tls->isManaged()) {
            return [];
        }

        $label = 'Certificado TLS de la API';
        $current = $this->tls->current();

        if ($current === null) {
            return [$this->fail('tls', $label, 'No hay certificado vigente', 'Ejecute deploy/tls-rotate.sh como root.')];
        }

        $expiresAt = $current['expires_at'];
        $days = (int) now()->diffInDays($expiresAt, false);
        $next = $this->tls->next();

        return [match (true) {
            $days < 7 => $this->fail('tls', $label, $days < 0 ? "Vencido el {$expiresAt->toDateString()}" : "Vence en {$days} días ({$expiresAt->toDateString()})", $next !== null && ! $this->tls->isNextAcknowledged()
                ? 'La plataforma no ha descargado el siguiente (GET /v1/platform/tls): revise su sincronización diaria.'
                : 'Revise el timer gateway-tls (systemctl status gateway-tls.timer).'),
            $next === null && $days < 30 => $this->warn('tls', $label, "Vence en {$days} días y aún no hay siguiente", 'Revise el timer gateway-tls (systemctl status gateway-tls.timer).'),
            $next !== null && ! $this->tls->isNextAcknowledged() && $days < 20 => $this->warn('tls', $label, "Vence en {$days} días; la plataforma aún no descargó el siguiente", 'La plataforma debe consultar GET /v1/platform/tls a diario.'),
            default => $this->ok('tls', $label, "Vence el {$expiresAt->toDateString()}".($next !== null ? '; siguiente listo' : '')),
        }];
    }

    private function ledger(): array
    {
        $beat = Heartbeat::query()->find('ledger-verify');

        return match (true) {
            $beat === null => $this->ok('ledger', 'Registro de emisiones', 'Aún sin verificar (se verifica cada día a las 03:30)'),
            ! ($beat->meta['ok'] ?? false) => $this->fail('ledger', 'Registro de emisiones', "Alterado en el correlativo {$beat->meta['sequence']}: {$beat->meta['reason']}", 'Incidente de seguridad: revise la auditoría y los respaldos.'),
            default => $this->ok('ledger', 'Registro de emisiones', 'Íntegro; verificado '.$this->ago($beat->beat_at)),
        };
    }

    private function ago(CarbonInterface $at): string
    {
        return CarbonImmutable::parse($at)->locale('es')->diffForHumans();
    }

    /** @return array{key: string, label: string, status: string, detail: string, hint: string|null} */
    private function ok(string $key, string $label, string $detail): array
    {
        return ['key' => $key, 'label' => $label, 'status' => 'ok', 'detail' => $detail, 'hint' => null];
    }

    /** @return array{key: string, label: string, status: string, detail: string, hint: string|null} */
    private function warn(string $key, string $label, string $detail, string $hint): array
    {
        return ['key' => $key, 'label' => $label, 'status' => 'warn', 'detail' => $detail, 'hint' => $hint];
    }

    /** @return array{key: string, label: string, status: string, detail: string, hint: string|null} */
    private function fail(string $key, string $label, string $detail, string $hint): array
    {
        return ['key' => $key, 'label' => $label, 'status' => 'fail', 'detail' => $detail, 'hint' => $hint];
    }
}
