<?php

namespace App\Jobs;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Exceptions\ProviderRequestException;
use App\Jobs\Concerns\HandlesProviderSession;
use App\Models\SignatureRequest;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

/**
 * Tramo C, paso 1: envía al proveedor el resultado de la prueba de vida hecha en el navegador.
 *
 * - Coincide: la solicitud sigue en VALIDATING y IssueCertificate (siguiente en la cadena) emite.
 * - No coincide y el proveedor deja reintentar (sessionStatus RETRYABLE/ERROR/FAILED o
 *   remainingAttempts > 0): FAILED_RETRYABLE; el SaaS puede pedir otra sesión de prueba de vida.
 * - Intentos agotados (sessionStatus REJECTED) o titular inexistente: REJECTED.
 */
#[Tries(3)]
#[Backoff(5, 15)]
class SubmitFaceLiveness implements ShouldBeEncrypted, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public function __construct(public SignatureRequest $signatureRequest, public string $livenessSessionId) {}

    public function handle(): void
    {
        try {
            $result = $this->provider()->submitLiveness($this->signatureRequest, $this->livenessSessionId);
        } catch (ProviderRequestException $exception) {
            if ($exception->isRetryable()) {
                throw $exception;
            }

            if ($exception->httpStatus === 409 && $exception->allowsApplicantRetry()) {
                $this->advance(RequestStatus::failedRetryable(), SessionStep::liveness(), 'session.liveness_failed', [
                    'provider_status' => $exception->sessionStatus,
                    'remaining_liveness_attempts' => $exception->remainingAttempts,
                    'last_error' => $exception->toArray(),
                ]);

                return;
            }

            $this->reject($exception, SessionStep::liveness());

            return;
        }

        if (! $result->biometricMatch) {
            $this->advance(RequestStatus::failedRetryable(), SessionStep::liveness(), 'session.liveness_failed', [
                'provider_status' => $result->sessionStatus,
                'last_error' => ['http_status' => 200, 'message' => 'La comparación biométrica no coincidió.', 'session_status' => $result->sessionStatus],
            ]);

            return;
        }

        $this->advance(null, SessionStep::liveness(), 'session.liveness_passed', [
            'provider_status' => $result->sessionStatus,
            'last_error' => null,
        ]);
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::validating()];
    }
}
