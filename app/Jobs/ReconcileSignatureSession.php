<?php

namespace App\Jobs;

use App\Exceptions\TrackingBudgetExhaustedException;
use App\Models\SignatureRequest;
use App\Signing\ApiFlow\SessionReconciler;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Attributes\UniqueFor;

/**
 * Consulta el tracking del proveedor para una solicitud abierta y corrige su estado.
 * Lo encola el comando gateway:reconcile-sessions dentro del presupuesto horario.
 */
#[Tries(2)]
#[Backoff(60)]
#[UniqueFor(600)]
class ReconcileSignatureSession implements ShouldBeEncrypted, ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {}

    public function uniqueId(): string
    {
        return $this->signatureRequest->getKey();
    }

    public function handle(SessionReconciler $reconciler): void
    {
        try {
            $reconciler->reconcile($this->signatureRequest);
        } catch (TrackingBudgetExhaustedException) {
            // El presupuesto se repone en la próxima ventana; el comando volverá a encolarla.
        }
    }
}
