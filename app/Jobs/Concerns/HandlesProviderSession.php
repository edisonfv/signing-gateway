<?php

namespace App\Jobs\Concerns;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Exceptions\ProviderRequestException;
use App\Models\ClientNotification;
use App\Models\SignatureRequest;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\SignatureProviderManager;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;

/**
 * Comportamiento común de los jobs del Flujo por API.
 *
 * Cada job declara en qué estados puede correr; si la solicitud ya no está en ese estado
 * (rechazada, cancelada o adelantada por otro job) se omite sin fallar, lo que también
 * corta una cadena sin llenar failed_jobs.
 *
 * @property SignatureRequest $signatureRequest
 */
trait HandlesProviderSession
{
    /**
     * @return array<int, RequestStatus>
     */
    abstract protected function runnableStatuses(): array;

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            Skip::unless(fn (): bool => $this->signatureRequest->fresh()->status->equals(...$this->runnableStatuses())),
            (new WithoutOverlapping($this->signatureRequest->getKey()))->releaseAfter(5)->expireAfter(180),
        ];
    }

    protected function provider(): DrivesApiFlow
    {
        return app(SignatureProviderManager::class)->apiFlowFor($this->signatureRequest);
    }

    /**
     * Cambia el estado (si corresponde), guarda los datos del paso y avisa al SaaS, todo en una transacción.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function advance(?RequestStatus $status, SessionStep $step, string $event, array $attributes = []): void
    {
        DB::transaction(function () use ($status, $step, $event, $attributes) {
            $request = $this->signatureRequest;
            $request->forceFill(['current_step' => $step, ...$attributes])->save();

            if ($status !== null && ! $request->status->equals($status)) {
                $request->transitionTo($status, 'job', ['step' => $step->value]);
            }

            $this->notifyClient($event);
        });
    }

    /**
     * Error de negocio del proveedor: la solicitud no puede continuar.
     */
    protected function reject(ProviderRequestException $exception, SessionStep $step): void
    {
        $this->advance(RequestStatus::rejected(), $step, 'session.rejected', [
            'provider_status' => $exception->sessionStatus,
            'last_error' => $exception->toArray(),
        ]);
    }

    protected function notifyClient(string $event): void
    {
        $request = $this->signatureRequest;

        ClientNotification::queue($request->apiClient, $event, [
            'signature_session_id' => $request->getKey(),
            'status' => $request->status->value,
            'step' => $request->current_step?->value,
            'remaining_liveness_attempts' => $request->remaining_liveness_attempts,
        ], $request);
    }
}
