<?php

namespace App\Jobs\Middleware;

use App\Actions\CancelSignatureSession;
use App\Exceptions\ProviderRequestException;
use App\Models\AuditLog;
use App\Signing\EmissionGuard;
use Closure;

/**
 * Antes de cada llamada al proveedor: si el afiliado fue suspendido o bloqueado, la credencial
 * revocada o la emisión detenida, el job no llama al proveedor y la solicitud se cancela.
 */
class StopWhenEmissionBlocked
{
    public function handle(object $job, Closure $next): void
    {
        $request = $job->signatureRequest->fresh(['tenant', 'apiClient', 'providerAccount']);
        $reason = app(EmissionGuard::class)->blockReason($request);

        if ($reason === null) {
            $next($job);

            return;
        }

        AuditLog::record('signature_session.stopped', subject: $request, context: ['job' => $job::class, 'reason' => $reason], ip: 'queue');
        $cancel = app(CancelSignatureSession::class);

        if (! $cancel->canCancel($request)) {
            return;
        }

        try {
            $cancel($request, 'guard', reason: $reason);
        } catch (ProviderRequestException $exception) {
            // La solicitud no avanza igual: el job no se ejecuta. La reconciliación verá su estado real.
            AuditLog::record('signature_session.cancel_failed', subject: $request, context: $exception->toArray(), ip: 'queue');
        }
    }
}
