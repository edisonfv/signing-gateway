<?php

namespace App\Http\Controllers\Api\V1\Platform;

use App\Http\Controllers\Controller;
use App\Signing\Health\GatewayHealth;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Plataforma', 'Solo para la credencial de servicio de la plataforma (ability platform.service).', weight: 11)]
class HealthController extends Controller
{
    /**
     * Estado de salud del gateway
     *
     * Para el monitor de la plataforma (consultarlo cada minuto): scheduler, cada cola, jobs fallidos,
     * notificaciones, proveedor y su cupo de consultas, reconciliación, freno de emisión, credencial de
     * alta y registro de emisiones. `status` es `ok`, `degraded` (algún aviso) o `down` (alguna falla);
     * cada comprobación trae `detail` y, si no está bien, `hint` con qué hacer. Responde siempre 200:
     * si el gateway no responde, eso ya es una caída.
     */
    public function show(GatewayHealth $health): JsonResponse
    {
        return response()->json($health->report())->header('Cache-Control', 'no-store');
    }
}
