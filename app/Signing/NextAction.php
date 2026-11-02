<?php

namespace App\Signing;

use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Enums\SignatureFlow;
use App\Models\SignatureRequest;

/**
 * Qué debe hacer el SaaS ahora con una solicitud, sin conocer el proveedor ni su flujo.
 *
 * Tipos: START_LIVENESS, FACE_LIVENESS, CORRECT_DATA, RETRIEVE_CERTIFICATE y WAIT. Null cuando la
 * solicitud ya no requiere nada (cerrada, o entregada por el proveedor al titular).
 */
final class NextAction
{
    /**
     * @return array{type: string, method: string|null, path: string|null}|null
     */
    public static function for(SignatureRequest $request): ?array
    {
        $status = $request->status;
        $base = "/v1/signature-sessions/{$request->getKey()}";

        if ($status->equals(RequestStatus::certificateStored()) && $request->flow->equals(SignatureFlow::livenessApi())) {
            return self::make('RETRIEVE_CERTIFICATE', 'GET', "{$base}/certificate");
        }

        if ($status->isTerminal() || $status->equals(RequestStatus::issued(), RequestStatus::certificateStored())) {
            return null;
        }

        if ($request->flow->equals(SignatureFlow::livenessApi())) {
            if ($status->equals(RequestStatus::awaitingApplicant()) && $request->current_step?->equals(SessionStep::livenessSession())) {
                return self::make('FACE_LIVENESS', 'GET', "{$base}/liveness-credentials");
            }

            if ($status->equals(RequestStatus::awaitingApplicant(), RequestStatus::failedRetryable())) {
                return self::make('START_LIVENESS', 'POST', "{$base}/liveness-session");
            }
        }

        if ($request->flow->equals(SignatureFlow::documentReview()) && $status->equals(RequestStatus::failedRetryable())) {
            return self::make('CORRECT_DATA', 'POST', "{$base}/corrections");
        }

        return self::make('WAIT');
    }

    /**
     * @return array{type: string, method: string|null, path: string|null}
     */
    private static function make(string $type, ?string $method = null, ?string $path = null): array
    {
        return ['type' => $type, 'method' => $method, 'path' => $path];
    }
}
