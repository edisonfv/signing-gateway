<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PairPlatform;
use App\Http\Controllers\Controller;
use App\Security\TlsTrust;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Plataforma', weight: 11)]
class PairingController extends Controller
{
    /**
     * Emparejar la plataforma
     *
     * Canjea el código de un solo uso que el operador generó con `php artisan gateway:pair` por la
     * credencial de alta de la plataforma (ability `clients.manage`), limitada a la IP que lo canjea.
     * Sin token ni firma: solo se acepta desde la red de la plataforma, con pocos intentos por minuto.
     * Devuelve el token y el secreto UNA sola vez; las credenciales anteriores de la plataforma quedan revocadas.
     * `tls_certificates` trae los certificados (PEM) en los que la plataforma debe confiar al llamar a la API;
     * desde ahí los mantiene al día con `GET /v1/platform/tls`. Vacío si el gateway no administra su certificado.
     */
    public function store(Request $request, PairPlatform $pair, TlsTrust $tls): JsonResponse
    {
        $data = $request->validate([
            /** Código de `gateway:pair`, p. ej. `K7PX-92QM`. */
            'code' => ['required', 'string', 'max:20'],
        ]);

        $credentials = $pair->redeem($data['code'], (string) $request->ip());

        abort_if($credentials === null, 422, 'Código inválido, usado o vencido. Genere uno nuevo con php artisan gateway:pair.');

        $tls->acknowledge();

        return response()->json([
            'client' => $credentials['client']->getKey(),
            'token' => $credentials['token'],
            'signing_secret' => $credentials['signing_secret'],
            'token_expires_at' => $credentials['expires_at'],
            'tls_certificates' => array_column($tls->certificates(), 'pem'),
        ], 201)->header('Cache-Control', 'no-store, private');
    }
}
