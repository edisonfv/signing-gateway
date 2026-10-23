<?php

namespace App\Jobs;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Exceptions\ProviderRequestException;
use App\Jobs\Concerns\HandlesProviderSession;
use App\Models\SignatureDocument;
use App\Models\SignatureRequest;
use App\Signing\SignatureProviderManager;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;

/**
 * Firmas Regulares: registra la solicitud con sus documentos (POST /collector/request) y
 * borra los documentos del gateway apenas el proveedor los recibe.
 *
 * Reintentar es seguro: si un intento anterior sí llegó (409 "ya existe"), se recupera el
 * requestId consultando el estado por referenceTransaction.
 */
#[Tries(3)]
#[Backoff(30, 120)]
#[Timeout(150)]
class SubmitRegularRequest implements ShouldBeEncrypted, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {}

    public function handle(SignatureProviderManager $providers): void
    {
        $provider = $providers->regularFlowFor($this->signatureRequest);

        try {
            $requestId = $provider->registerRegularRequest($this->signatureRequest);
        } catch (ProviderRequestException $exception) {
            if ($exception->isRetryable()) {
                throw $exception;
            }

            if ($exception->httpStatus !== 409 || ! str_contains(mb_strtolower($exception->getMessage()), 'ya existe')) {
                $this->reject($exception, SessionStep::request());

                return;
            }

            $requestId = $provider->regularRequestState($this->signatureRequest)->requestId;
        }

        $this->advance(RequestStatus::submitted(), SessionStep::request(), 'session.submitted', [
            'provider_request_id' => $requestId,
            'provider_status' => 'REGISTERED',
        ]);

        $this->signatureRequest->documents()->whereNull('purged_at')->get()->each(fn (SignatureDocument $document) => $document->purge());
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::submitting()];
    }
}
