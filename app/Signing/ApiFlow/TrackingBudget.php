<?php

namespace App\Signing\ApiFlow;

use App\Models\ProviderAccount;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Presupuesto de consultas de estado por cuenta de proveedor.
 *
 * FirmaSegura permite como máximo 1000 consultas de estado por hora; el gateway usa
 * un tope configurable (900 por defecto) para dejar margen. Lo comparten la
 * reconciliación programada y las consultas bajo demanda.
 */
class TrackingBudget
{
    private const WINDOW_SECONDS = 3600;

    /**
     * Reserva una consulta. Devuelve false si se agotó el presupuesto de la hora.
     */
    public function consume(ProviderAccount $account): bool
    {
        return RateLimiter::hit($this->key($account), self::WINDOW_SECONDS) <= $this->limit();
    }

    public function remaining(ProviderAccount $account): int
    {
        return RateLimiter::remaining($this->key($account), $this->limit());
    }

    public function limit(): int
    {
        return config('gateway.limits.provider_status_hourly');
    }

    private function key(ProviderAccount $account): string
    {
        return "provider-tracking:{$account->getKey()}";
    }
}
