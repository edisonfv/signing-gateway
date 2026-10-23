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
 * Tramo A, paso 2: valida al titular en el Registro Civil. Si pasa, la solicitud queda
 * esperando que el titular haga la prueba de vida.
 *
 * Rechazos de negocio del proveedor: no existe (404), menor de edad, estado civil no apto
 * o cédula caducada (409), datos incompletos (422).
 */
#[Tries(3)]
#[Backoff(10, 30)]
class ValidateCivilRegistry implements ShouldBeEncrypted, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {}

    public function handle(): void
    {
        try {
            $result = $this->provider()->validateCivilRegistry($this->signatureRequest);
        } catch (ProviderRequestException $exception) {
            if ($exception->isRetryable()) {
                throw $exception;
            }

            $this->reject($exception, SessionStep::civilRegistry());

            return;
        }

        if (! $result->verified) {
            $this->advance(RequestStatus::rejected(), SessionStep::civilRegistry(), 'session.rejected', [
                'last_error' => ['http_status' => 200, 'message' => 'El Registro Civil no verificó al titular.', 'session_status' => null],
            ]);

            return;
        }

        $this->advance(RequestStatus::awaitingApplicant(), SessionStep::civilRegistry(), 'session.awaiting_liveness');
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::submitted()];
    }
}
