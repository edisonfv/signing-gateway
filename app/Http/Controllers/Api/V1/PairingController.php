<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PairPlatform;
use App\Http\Controllers\Controller;
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
     */
    public function store(Request $request, PairPlatform $pair): JsonResponse
    {
        $data = $request->validate([
            /** Código de `gateway:pair`, p. ej. `K7PX-92QM`. */
            'code' => ['required', 'string', 'max:20'],
        ]);

        $credentials = $pair->redeem($data['code'], (string) $request->ip());

        abort_if($credentials === null, 422, 'Código inválido, usado o vencido. Genere uno nuevo con php artisan gateway:pair.');

        return response()->json([
            'client' => $credentials['client']->getKey(),
            'token' => $credentials['token'],
            'signing_secret' => $credentials['signing_secret'],
            'token_expires_at' => $credentials['expires_at'],
        ], 201)->header('Cache-Control', 'no-store, private');
    }
}
