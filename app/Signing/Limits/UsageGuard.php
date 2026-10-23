<?php

namespace App\Signing\Limits;

use App\Events\LimitExceeded;
use App\Exceptions\LimitExceededException;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Topes duros de emisión, independientes de los cupos del SaaS.
 *
 * Aunque el SaaS esté comprometido, el gateway no emite más de lo permitido por tenant
 * (hora y 24 h) ni más del tope global por hora. El conteo y la creación ocurren bajo
 * un lock atómico para que peticiones simultáneas no superen el tope.
 */
class UsageGuard
{
    /**
     * Ejecuta $create solo si el tenant y el gateway están dentro de sus topes.
     *
     * @template TResult
     *
     * @param  Closure(): TResult  $create
     * @return TResult
     *
     * @throws LimitExceededException
     */
    public function within(Tenant $tenant, Closure $create): mixed
    {
        return Cache::lock('usage-guard:global', 10)->block(5, function () use ($tenant, $create) {
            $this->ensureBelow($tenant, 'tenant_hourly', $tenant->hourlyCap(), $this->countSince($tenant, now()->subHour()));
            $this->ensureBelow($tenant, 'tenant_daily', $tenant->dailyCap(), $this->countSince($tenant, now()->subDay()));
            $this->ensureBelow($tenant, 'global_hourly', config('gateway.limits.global_hourly'), $this->countSince(null, now()->subHour()));

            return $create();
        });
    }

    private function countSince(?Tenant $tenant, \DateTimeInterface $since): int
    {
        return SignatureRequest::query()
            ->when($tenant, fn ($query) => $query->whereBelongsTo($tenant))
            ->where('created_at', '>=', $since)
            ->count();
    }

    private function ensureBelow(Tenant $tenant, string $scope, int $limit, int $current): void
    {
        if ($current < $limit) {
            return;
        }

        AuditLog::record('limits.exceeded', subject: $tenant, context: ['scope' => $scope, 'limit' => $limit, 'current' => $current]);

        LimitExceeded::dispatch($tenant, $scope, $limit, $current);

        throw new LimitExceededException($scope, $limit);
    }
}
