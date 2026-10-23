<?php

namespace App\Http\Controllers\Dev;

use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\SignatureSessionResource;
use App\Jobs\IssueCertificate;
use App\Jobs\RequestLivenessSession;
use App\Jobs\SubmitFaceLiveness;
use App\Models\SignatureRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Página de prueba de vida SOLO para el entorno local (routes/web.php la registra solo en local).
 *
 * Simula al SaaS desde el servidor: corre los jobs del Flujo por API en forma síncrona,
 * así el navegador nunca ve el token ni el secreto de firma del cliente. Al navegador solo
 * llegan las credenciales AWS temporales de la prueba de vida, igual que en producción.
 */
class LivenessTestController extends Controller
{
    public function show(?SignatureRequest $signatureRequest = null): View
    {
        $pending = SignatureRequest::query()
            ->where('product', ProductType::headless()->value)
            ->whereIn('status', [RequestStatus::awaitingApplicant()->value, RequestStatus::failedRetryable()->value])
            ->latest()
            ->limit(20)
            ->get();

        return view('dev.liveness', [
            'pending' => $pending,
            'selected' => $signatureRequest,
        ]);
    }

    /**
     * Pide al proveedor una sesión de prueba de vida y devuelve las credenciales temporales.
     */
    public function session(SignatureRequest $signatureRequest): JsonResponse
    {
        abort_unless(
            $signatureRequest->status->equals(RequestStatus::awaitingApplicant(), RequestStatus::failedRetryable()),
            409,
            "La solicitud está en {$signatureRequest->status->value}; no espera una prueba de vida.",
        );

        RequestLivenessSession::dispatchSync($signatureRequest);

        $encrypted = Cache::pull(RequestLivenessSession::cacheKey($signatureRequest));
        abort_if($encrypted === null, 502, 'El proveedor no devolvió una sesión de prueba de vida.');

        return response()->json(json_decode(Crypt::decryptString($encrypted), true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * Envía el resultado al proveedor y, si coincide, emite el certificado (síncrono).
     */
    public function result(Request $request, SignatureRequest $signatureRequest): JsonResponse
    {
        $livenessSessionId = $request->validate(['liveness_session_id' => ['required', 'string', 'max:100']])['liveness_session_id'];

        abort_unless($signatureRequest->status->equals(RequestStatus::awaitingApplicant()), 409, 'La solicitud no espera un resultado de prueba de vida.');

        $signatureRequest->transitionTo(RequestStatus::validating(), 'dev');

        SubmitFaceLiveness::dispatchSync($signatureRequest, $livenessSessionId);
        IssueCertificate::dispatchSync($signatureRequest);

        return SignatureSessionResource::make($signatureRequest->refresh())->response();
    }
}
