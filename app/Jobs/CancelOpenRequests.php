<?php

namespace App\Jobs;

use App\Actions\CancelSignatureSession;
use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Cancela en el proveedor las solicitudes abiertas, aún no emitidas, de un afiliado detenido.
 * Las de Firmas Regulares ya registradas no se pueden cancelar: el proveedor sigue con ellas.
 */
class CancelOpenRequests implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120];

    public function __construct(public Tenant $tenant, public string $reason) {}

    public function handle(CancelSignatureSession $cancel): void
    {
        SignatureRequest::query()
            ->whereBelongsTo($this->tenant)
            ->with(['apiClient', 'providerAccount', 'tenant'])
            ->whereIn('status', collect(RequestStatus::cases())
                ->filter(fn (RequestStatus $status) => $status->canTransitionTo(RequestStatus::cancelled()))
                ->map(fn (RequestStatus $status) => $status->value)
                ->all())
            ->get()
            ->filter(fn (SignatureRequest $request) => $cancel->canCancel($request))
            ->each(function (SignatureRequest $request) use ($cancel) {
                try {
                    $cancel($request, 'suspension', reason: $this->reason);
                } catch (ProviderRequestException $exception) {
                    AuditLog::record('signature_session.cancel_failed', subject: $request, context: $exception->toArray(), ip: 'queue');
                }
            });
    }
}
