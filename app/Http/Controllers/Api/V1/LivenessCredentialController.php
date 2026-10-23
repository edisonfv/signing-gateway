<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\RequestLivenessSession;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class LivenessCredentialController extends Controller
{
    /**
     * Retirar credenciales de prueba de vida
     *
     * Devuelve **una sola vez** el `liveness_session_id` y las credenciales AWS temporales para el
     * SDK del navegador (AWS Amplify `FaceLivenessDetector`). Una segunda llamada responde 404.
     */
    public function show(SignatureRequest $signatureSession): JsonResponse
    {
        Gate::authorize('view', $signatureSession);

        $encrypted = Cache::pull(RequestLivenessSession::cacheKey($signatureSession));

        abort_if($encrypted === null, 404, 'No hay credenciales de prueba de vida pendientes de retiro.');

        AuditLog::record('liveness.credentials_retrieved', request()->user(), $signatureSession);

        return response()
            ->json(['data' => json_decode(Crypt::decryptString($encrypted), true, flags: JSON_THROW_ON_ERROR)])
            ->header('Cache-Control', 'no-store');
    }
}
