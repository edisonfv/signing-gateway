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
 * Tramo A, paso 1: registra la solicitud en el proveedor (POST /signature/api/request).
 *
 * Reintentar es seguro: referenceTransaction es el UUID de la solicitud y el proveedor
 * rechaza duplicados. La contraseña que eligió el titular (si la hay) se borra en cuanto el
 * proveedor la acepta, o al cerrarse la solicitud (ver SignatureRequest::transitionTo).
 */
#[Tries(3)]
#[Backoff(10, 30)]
class CreateProviderSession implements ShouldBeEncrypted, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public const WEAK_PASSWORD_MESSAGE = 'FirmaSegura no aceptó la contraseña elegida. Inicia una nueva solicitud con otra contraseña (solo letras y números) o deja que el sistema la cree.';

    public function __construct(public SignatureRequest $signatureRequest) {}

    public function handle(): void
    {
        try {
            $session = $this->provider()->createSession($this->signatureRequest);
        } catch (ProviderRequestException $exception) {
            if ($exception->isRetryable()) {
                throw $exception;
            }

            // FirmaSegura dice "puede volver a intentarlo", pero la solicitud queda cerrada.
            if ($this->signatureRequest->applicant_password !== null && str_contains(mb_strtolower($exception->getMessage()), 'contraseña')) {
                $exception = $exception->withMessage(self::WEAK_PASSWORD_MESSAGE);
            }

            $this->reject($exception, SessionStep::request());

            return;
        }

        $this->advance(RequestStatus::submitted(), SessionStep::request(), 'session.submitted', [
            'provider_session_id' => $session->sessionId,
            'provider_status' => $session->status,
            'applicant_password' => null,
        ]);
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::submitting()];
    }
}
