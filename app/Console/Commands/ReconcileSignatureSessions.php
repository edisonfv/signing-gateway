<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Jobs\ReconcileSignatureSession;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\ApiFlow\TrackingBudget;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Encola la reconciliación de las solicitudes abiertas, sin pasar el presupuesto de consultas.
 *
 * Prioridad: nunca reconciliadas primero, luego las que llevan más tiempo sin consulta.
 * Recientes (< 24 h) cada 10 min; antiguas cada 60 min. Se omiten las que cambiaron hace
 * menos de 2 min porque su propio job puede estar corriendo.
 */
#[Signature('gateway:reconcile-sessions')]
#[Description('Consulta el tracking del proveedor para las solicitudes abiertas y corrige su estado')]
class ReconcileSignatureSessions extends Command
{
    public function handle(TrackingBudget $budget): int
    {
        $total = 0;

        foreach (ProviderAccount::where('is_active', true)->get() as $account) {
            $available = $budget->remaining($account);

            if ($available <= 0) {
                $this->components->warn("Presupuesto de consultas agotado para {$account->name}.");

                continue;
            }

            $candidates = $this->candidates($account)->limit($available)->get();
            $candidates->each(fn (SignatureRequest $request) => ReconcileSignatureSession::dispatch($request));
            $total += $candidates->count();
        }

        $this->components->info("Solicitudes encoladas para reconciliar: {$total}.");

        return self::SUCCESS;
    }

    /**
     * @return Builder<SignatureRequest>
     */
    private function candidates(ProviderAccount $account): Builder
    {
        $config = config('gateway.reconciliation');
        $staleThreshold = now()->subHours($config['stale_after_hours']);

        return SignatureRequest::query()
            ->whereBelongsTo($account)
            ->whereIn('product', [ProductType::headless()->value, ProductType::regular()->value])
            ->where(fn (Builder $query) => $query->whereNotNull('provider_session_id')->orWhereNotNull('provider_request_id'))
            ->where('updated_at', '<=', now()->subSeconds($config['quiet_seconds']))
            ->where(fn (Builder $query) => $query
                ->whereIn('status', [
                    RequestStatus::submitted()->value,
                    RequestStatus::awaitingApplicant()->value,
                    RequestStatus::validating()->value,
                    RequestStatus::failedRetryable()->value,
                    RequestStatus::issued()->value,
                ])
                ->orWhere(fn (Builder $failedIssuance) => $failedIssuance
                    ->where('status', RequestStatus::failed()->value)
                    ->where('current_step', SessionStep::issuance()->value)
                    ->where('created_at', '>=', now()->subDays(7))))
            ->where(fn (Builder $query) => $query
                ->whereNull('last_reconciled_at')
                ->orWhere(fn (Builder $recent) => $recent
                    ->where('created_at', '>=', $staleThreshold)
                    ->where('last_reconciled_at', '<=', now()->subMinutes($config['recent_interval_minutes'])))
                ->orWhere(fn (Builder $stale) => $stale
                    ->where('created_at', '<', $staleThreshold)
                    ->where('last_reconciled_at', '<=', now()->subMinutes($config['stale_interval_minutes']))))
            ->orderByRaw('last_reconciled_at IS NOT NULL')
            ->orderBy('last_reconciled_at')
            ->orderByDesc('created_at');
    }
}
