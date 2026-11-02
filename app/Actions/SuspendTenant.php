<?php

namespace App\Actions;

use App\Jobs\CancelOpenRequests;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Detiene a un afiliado de inmediato.
 *
 * - suspend (comercial): no crea ni avanza solicitudes; aún consulta y retira lo ya emitido.
 * - block (incidente): además revoca todas sus credenciales; no puede hacer nada.
 *
 * En ambos casos se cancelan en el proveedor sus solicitudes abiertas no emitidas, y los jobs
 * en cola se frenan antes de llamar al proveedor (StopWhenEmissionBlocked).
 */
class SuspendTenant
{
    public function __construct(private RevokeApiClient $revoke) {}

    public function suspend(Tenant $tenant, ?Model $actor = null, ?string $reason = null): void
    {
        $this->stop($tenant, block: false, actor: $actor, reason: $reason);
    }

    public function block(Tenant $tenant, ?Model $actor = null, ?string $reason = null): void
    {
        $this->stop($tenant, block: true, actor: $actor, reason: $reason);
    }

    /**
     * Reactiva al afiliado. Si estaba bloqueado, sus credenciales siguen revocadas: hay que emitir nuevas.
     */
    public function resume(Tenant $tenant, ?Model $actor = null): void
    {
        $tenant->forceFill(['suspended_at' => null, 'blocked_at' => null])->save();
        AuditLog::record('tenant.resumed', $actor, $tenant, ip: $actor ? null : 'cli');
    }

    private function stop(Tenant $tenant, bool $block, ?Model $actor, ?string $reason): void
    {
        DB::transaction(function () use ($tenant, $block, $actor, $reason) {
            $tenant->forceFill([
                'suspended_at' => $tenant->suspended_at ?? now(),
                'blocked_at' => $block ? ($tenant->blocked_at ?? now()) : $tenant->blocked_at,
            ])->save();

            if ($block) {
                $tenant->apiClients()->whereNull('revoked_at')->get()
                    ->each(fn (ApiClient $client) => ($this->revoke)($client, $actor instanceof ApiClient ? $actor : null));
            }

            AuditLog::record($block ? 'tenant.blocked' : 'tenant.suspended', $actor, $tenant, context: array_filter(['reason' => $reason]), ip: $actor ? null : 'cli');
        });

        CancelOpenRequests::dispatch($tenant, $block ? 'El afiliado fue bloqueado.' : 'El afiliado fue suspendido.');
    }
}
