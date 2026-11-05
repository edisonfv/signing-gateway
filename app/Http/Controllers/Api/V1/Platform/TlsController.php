<?php

namespace App\Http\Controllers\Api\V1\Platform;

use App\Http\Controllers\Controller;
use App\Security\TlsTrust;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Plataforma', weight: 11)]
class TlsController extends Controller
{
    /**
     * Certificados de confianza de la API
     *
     * Los certificados (PEM) en los que la plataforma debe confiar al llamar al gateway: el vigente y,
     * desde 30 días antes de que venza, el siguiente. La plataforma lo consulta a diario y reemplaza su
     * lista de confianza con esta respuesta; el gateway solo cambia al siguiente después de que la
     * plataforma lo haya descargado, así que la conexión no se corta. Lista vacía si el gateway no
     * administra su certificado.
     */
    public function show(TlsTrust $tls): JsonResponse
    {
        $tls->acknowledge();

        return response()->json([
            'certificates' => array_map(fn (array $certificate) => [
                'pem' => $certificate['pem'],
                'fingerprint' => $certificate['fingerprint'],
                'expires_at' => $certificate['expires_at']->toIso8601String(),
            ], $tls->certificates()),
        ])->header('Cache-Control', 'no-store');
    }
}
