<?php

namespace App\Jobs;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Jobs\Concerns\HandlesProviderSession;
use App\Models\AuditLog;
use App\Models\CertificateVaultEntry;
use App\Models\SignatureRequest;
use App\Signing\Drivers\FirmaSegura\Enums\SessionStage;
use App\Signing\Drivers\FirmaSegura\Enums\SessionStatus;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\FailOnTimeout;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Tramo C, paso 2: emite el certificado (POST …/issuance) y lo guarda en la bóveda.
 *
 * Un solo intento: la emisión no es idempotente y no se sabe si el proveedor permite
 * recuperar un certificado cuya respuesta se perdió. Si falla, se registra un incidente
 * para revisión manual en lugar de reintentar a ciegas.
 */
#[Tries(1)]
#[Timeout(90)]
#[FailOnTimeout]
class IssueCertificate implements ShouldBeEncrypted, ShouldBeUnique, ShouldQueue
{
    use HandlesProviderSession, Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {}

    public function uniqueId(): string
    {
        return $this->signatureRequest->getKey();
    }

    /**
     * Solo emite si la prueba de vida de este mismo ciclo coincidió.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            Skip::unless(function (): bool {
                $request = $this->signatureRequest->fresh();

                return $request->status->equals(RequestStatus::validating())
                    && $request->current_step?->equals(SessionStep::liveness())
                    && $request->last_error === null;
            }),
            (new WithoutOverlapping($this->signatureRequest->getKey()))->dontRelease()->expireAfter(180),
        ];
    }

    public function handle(): void
    {
        $certificate = $this->provider()->issueCertificate($this->signatureRequest);

        DB::transaction(function () use ($certificate) {
            CertificateVaultEntry::create([
                'signature_request_id' => $this->signatureRequest->getKey(),
                'certificate' => $certificate->certificateBase64,
                'password' => $certificate->password,
                'certificate_sha256' => $certificate->sha256(),
                'serial_number' => $certificate->serialNumber,
                'received_at' => now(),
            ]);

            // Tras emitir, el tracking del proveedor reporta COMPLETED en la etapa CERTIFICATE.
            $this->signatureRequest->forceFill([
                'issued_at' => now(),
                'provider_status' => SessionStatus::completed()->value,
                'provider_stage' => SessionStage::certificate()->value,
            ])->save();
            $this->signatureRequest->transitionTo(RequestStatus::issued(), 'job');
            $this->advance(RequestStatus::certificateStored(), SessionStep::issuance(), 'certificate.ready');
        });

        if (! $certificate->opensWithPassword()) {
            AuditLog::record('certificate.unreadable_with_password', subject: $this->signatureRequest, ip: 'queue');
        }
    }

    public function failed(?Throwable $exception): void
    {
        $request = $this->signatureRequest->fresh();

        AuditLog::record('certificate.issuance_failed', subject: $request, context: [
            'exception' => $exception ? $exception::class : null,
        ], ip: 'queue');

        if ($request->status->canTransitionTo(RequestStatus::failed())) {
            $this->signatureRequest = $request;
            $this->advance(RequestStatus::failed(), SessionStep::issuance(), 'session.failed', [
                'last_error' => ['http_status' => null, 'message' => 'La emisión falló o quedó sin confirmar; requiere revisión manual.', 'session_status' => null],
            ]);
        }
    }

    protected function runnableStatuses(): array
    {
        return [RequestStatus::validating()];
    }
}
