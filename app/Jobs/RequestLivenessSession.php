<?php

namespace App\Jobs;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Jobs\Concerns\HandlesProviderSession;
use App\Models\SignatureRequest;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Tramo B: pide al proveedor una sesión de prueba de vida con credenciales AWS temporales.
 *
 * Las credenciales se guardan cifradas en caché hasta su vencimiento y el SaaS las retira
 * una sola vez (GET …/liveness-credentials). Nunca se escriben en la base ni en logs.
 */
#[Tries(3)]
#[Backoff(3, 10)]
class RequestLivenessSession implements ShouldBeEncrypted, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {}

    public static function cacheKey(SignatureRequest $request): string
    {
        return "liveness-credentials:{$request->getKey()}";
    }

    public function handle(): void
    {
        $credentials = $this->provider()->requestLivenessSession();

        Cache::put(
            self::cacheKey($this->signatureRequest),
            Crypt::encryptString(json_encode($credentials->toArray(), JSON_THROW_ON_ERROR)),
            $credentials->expiresAt,
        );

        $this->advance(RequestStatus::awaitingApplicant(), SessionStep::livenessSession(), 'session.liveness_ready');
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::awaitingApplicant(), RequestStatus::failedRetryable()];
    }
}
