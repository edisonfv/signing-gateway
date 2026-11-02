<?php

namespace App\Console\Commands;

use App\Enums\Environment;
use App\Models\ApiClient;
use App\Models\City;
use App\Models\ProviderAccount;
use App\Security\KeyRing;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\EmissionGuard;
use App\Signing\Ledger\IssuanceLedger;
use App\Signing\SignatureProviderManager;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Spatie\Permission\Models\Role;
use Throwable;

#[Signature('gateway:doctor {--offline : No consulta al proveedor}')]
#[Description('Verifica que el gateway esté bien instalado: llaves, base, proveedor, emparejamiento y red')]
class GatewayDoctor extends Command
{
    /** @var array<int, array{0: string, 1: string, 2: string}> */
    private array $rows = [];

    private bool $failed = false;

    public function handle(KeyRing $keys, SignatureProviderManager $providers, IssuanceLedger $ledger, EmissionGuard $guard): int
    {
        $this->checkKeys($keys);
        $this->checkDatabase();
        $this->checkProvider($providers);
        $this->checkPlatform();
        $this->checkProductionConfig();
        $this->check('Emisión', ! $guard->isHalted(), 'activa', 'DETENIDA (gateway:halt --off para reanudar)', fatal: false);

        try {
            $problem = $ledger->verify();
            $this->check('Registro de emisiones', $problem === null, 'íntegro', $problem === null ? '' : "alterado en el correlativo {$problem['sequence']}: {$problem['reason']}");
        } catch (Throwable $exception) {
            $this->check('Registro de emisiones', false, '', $exception->getMessage());
        }

        $this->table(['Comprobación', 'Estado', 'Detalle'], $this->rows);

        $this->failed
            ? $this->components->error('Hay problemas que resolver antes de operar.')
            : $this->components->info('Todo en orden.');

        return $this->failed ? self::FAILURE : self::SUCCESS;
    }

    private function checkKeys(KeyRing $keys): void
    {
        $this->check('APP_KEY', filled(config('app.key')), 'configurada', 'falta (php artisan key:generate)');

        foreach ([KeyRing::VAULT => 'GATEWAY_VAULT_KEY', KeyRing::VAULT_PASSWORD => 'GATEWAY_VAULT_PASSWORD_KEY'] as $purpose => $name) {
            try {
                $encrypter = $keys->encrypter($purpose);
                $this->check($name, $encrypter->decryptString($encrypter->encryptString('ok')) === 'ok', 'cifra y descifra', 'no descifra');
            } catch (Throwable) {
                $this->check($name, false, '', 'falta o es inválida (php artisan gateway:keys:generate)');
            }
        }

        try {
            $keys->blindIndexKey();
            $this->check('GATEWAY_BLIND_INDEX_KEY', true, 'configurada', '');
        } catch (Throwable) {
            $this->check('GATEWAY_BLIND_INDEX_KEY', false, '', 'falta o es inválida (php artisan gateway:keys:generate)');
        }

        // Con datos ya cifrados, una llave distinta (p. ej. tras mudar de servidor) se nota aquí.
        try {
            $sample = ApiClient::query()->first();
            $sample?->signing_secret;
            ProviderAccount::query()->first()?->credentials;
            $this->check('Datos cifrados existentes', true, $sample ? 'se descifran con las llaves actuales' : 'aún no hay datos', '');
        } catch (Throwable) {
            $this->check('Datos cifrados existentes', false, '', 'NO se descifran: las llaves no son las que cifraron estos datos');
        }
    }

    private function checkDatabase(): void
    {
        try {
            /** @var Migrator $migrator */
            $migrator = app('migrator');
            $pending = array_diff(array_keys($migrator->getMigrationFiles(database_path('migrations'))), $migrator->getRepository()->getRan());
            $this->check('Migraciones', $pending === [], 'al día', count($pending).' pendientes (php artisan migrate --force)');
            $this->check('Catálogos', City::query()->exists() && Role::query()->exists(), 'sembrados', 'faltan (php artisan db:seed --force)');
        } catch (Throwable $exception) {
            $this->check('Base de datos', false, '', $exception->getMessage());
        }
    }

    private function checkProvider(SignatureProviderManager $providers): void
    {
        $environment = Environment::from(config('gateway.environment'));
        $account = ProviderAccount::query()->where('environment', $environment->value)->where('is_active', true)->orderBy('priority')->first();

        if ($account === null) {
            $this->check("Proveedor ({$environment->value})", false, '', "sin cuenta activa (php artisan gateway:provider:credentials {$environment->value})");

            return;
        }

        if ($this->option('offline')) {
            $this->check("Proveedor ({$environment->value})", true, "{$account->name} (sin consultar)", '');

            return;
        }

        try {
            $driver = $providers->forAccount($account);
            $driver instanceof DrivesApiFlow && $driver->currentTerms();
            $this->check("Proveedor ({$environment->value})", true, "{$account->name} responde desde este servidor", '');
        } catch (Throwable $exception) {
            $this->check("Proveedor ({$environment->value})", false, '', "{$account->name} no responde: ".mb_substr($exception->getMessage(), 0, 120).' (¿token vigente? ¿IP del servidor autorizada en el proveedor?)');
        }
    }

    private function checkPlatform(): void
    {
        $paired = ApiClient::query()
            ->whereNull('tenant_id')
            ->whereNull('revoked_at')
            ->whereHas('tokens', fn ($query) => $query->where('abilities', 'like', '%clients.manage%')
                ->where(fn ($expiry) => $expiry->whereNull('expires_at')->orWhere('expires_at', '>', now())))
            ->exists();

        $this->check('Plataforma', $paired, 'emparejada', 'sin emparejar (php artisan gateway:pair)', fatal: false);
    }

    private function checkProductionConfig(): void
    {
        $production = $this->laravel->isProduction();
        $required = $production ? 'obligatoria en producción' : 'opcional fuera de producción';

        $this->check('GATEWAY_ALLOWED_SOURCES', config('gateway.network.allowed_sources') !== [], implode(', ', config('gateway.network.allowed_sources')), "vacía: {$required}", fatal: $production);
        $this->check('GATEWAY_NOTIFICATION_HOSTS', config('gateway.network.notification_hosts') !== [], implode(', ', config('gateway.network.notification_hosts')), "vacía: {$required}", fatal: $production);
        $this->check('GATEWAY_WEBHOOK_URL', filled(config('gateway.webhooks.url')), (string) config('gateway.webhooks.url'), 'vacía: sin avisos del proveedor, solo reconciliación', fatal: false);

        if ($production) {
            $this->check('APP_DEBUG', ! config('app.debug'), 'desactivado', 'activado: expone detalles de errores');
            $this->check('Colas', config('queue.default') !== 'sync', config('queue.default'), 'sync: los jobs no deben correr en la petición');
            $this->check('Caché', config('cache.default') !== 'array', config('cache.default'), 'array: los nonces y avisos no se recuerdan entre peticiones');
        }
    }

    private function check(string $name, bool $ok, string $okDetail, string $failDetail, bool $fatal = true): void
    {
        if (! $ok && $fatal) {
            $this->failed = true;
        }

        $this->rows[] = [$name, $ok ? '<fg=green>OK</>' : ($fatal ? '<fg=red>FALLA</>' : '<fg=yellow>AVISO</>'), $ok ? $okDetail : $failDetail];
    }
}
