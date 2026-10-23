<?php

namespace App\Signing\ApiFlow;

use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Exceptions\TrackingBudgetExhaustedException;
use App\Models\AuditLog;
use App\Models\ClientNotification;
use App\Models\SignatureRequest;
use App\Signing\Data\SessionTracking;
use App\Signing\Drivers\FirmaSegura\Enums\SessionStatus;
use App\Signing\SignatureProviderManager;
use Illuminate\Support\Facades\DB;

/**
 * Alinea el estado local de una solicitud con el tracking del proveedor.
 *
 * Solo corrige lo que el proveedor ya decidió: cierres (rechazo, expiración, anulación) y
 * pausas reintentables que el gateway no registró. Una sesión COMPLETED sin certificado en la
 * bóveda no se "arregla" sola: se registra como incidente para revisión manual.
 */
class SessionReconciler
{
    public function __construct(
        private SignatureProviderManager $providers,
        private TrackingBudget $budget,
    ) {}

    /**
     * @throws TrackingBudgetExhaustedException
     * @throws ProviderRequestException si el proveedor falla de forma transitoria
     */
    public function reconcile(SignatureRequest $request, string $source = 'reconcile'): SignatureRequest
    {
        if (! $this->budget->consume($request->providerAccount)) {
            throw new TrackingBudgetExhaustedException;
        }

        try {
            $tracking = $this->providers->apiFlowFor($request)->trackSession($request);
        } catch (ProviderRequestException $exception) {
            if ($exception->isRetryable()) {
                throw $exception;
            }

            $request->forceFill(['last_reconciled_at' => now()])->save();
            AuditLog::record('reconcile.provider_error', subject: $request, context: $exception->toArray(), ip: $source);

            return $request;
        }

        DB::transaction(fn () => $this->apply($request, $tracking, $source));

        return $request->refresh();
    }

    private function apply(SignatureRequest $request, SessionTracking $tracking, string $source): void
    {
        $request->forceFill([
            'provider_status' => $tracking->status,
            'provider_stage' => $tracking->stage,
            'last_reconciled_at' => now(),
        ])->save();

        $target = $this->targetStatus($request, $tracking);

        if ($target !== null && $request->status->canTransitionTo($target)) {
            if ($tracking->errorDetail !== null) {
                $request->forceFill(['last_error' => [
                    'http_status' => null,
                    'message' => $tracking->errorDetail,
                    'session_status' => $tracking->status,
                ]])->save();
            }

            $request->transitionTo($target, $source, ['provider_status' => $tracking->status, 'provider_error' => $tracking->errorCode]);
            $this->notify($request, 'session.reconciled');

            return;
        }

        if ($this->isIssuedButNotStored($request, $tracking)) {
            $alreadyReported = AuditLog::query()
                ->where('action', 'certificate.issued_not_stored')
                ->where('subject_id', $request->getKey())
                ->exists();

            if (! $alreadyReported) {
                AuditLog::record('certificate.issued_not_stored', subject: $request, context: ['status' => $request->status->value], ip: $source);
                $this->notify($request, 'session.needs_review');
            }
        }
    }

    private function targetStatus(SignatureRequest $request, SessionTracking $tracking): ?RequestStatus
    {
        return match ($tracking->status) {
            SessionStatus::rejected()->value => RequestStatus::rejected(),
            SessionStatus::expired()->value => RequestStatus::expired(),
            SessionStatus::cancelled()->value => RequestStatus::cancelled(),
            SessionStatus::retryable()->value, SessionStatus::error()->value, SessionStatus::failed()->value => $request->status->equals(RequestStatus::awaitingApplicant(), RequestStatus::validating())
                ? RequestStatus::failedRetryable()
                : null,
            default => null,
        };
    }

    /**
     * El proveedor terminó la emisión pero el p12 no está en la bóveda (respuesta perdida).
     */
    private function isIssuedButNotStored(SignatureRequest $request, SessionTracking $tracking): bool
    {
        return $tracking->status === SessionStatus::completed()->value
            && ! $request->status->equals(RequestStatus::certificateStored(), RequestStatus::delivered())
            && $request->vaultEntry === null;
    }

    private function notify(SignatureRequest $request, string $event): void
    {
        ClientNotification::queue($request->apiClient, $event, [
            'signature_session_id' => $request->getKey(),
            'status' => $request->status->value,
            'step' => $request->current_step?->value,
            'provider_status' => $request->provider_status,
        ], $request);
    }
}
