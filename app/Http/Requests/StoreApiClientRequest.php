<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            /** Nombre de la credencial (p. ej. "Comercial Demo · 2026-10-26"). */
            'name' => ['required', 'string', 'max:255'],
            /** Llave pública X25519 en base64 con la que se sellan los certificados de este afiliado. */
            'delivery_public_key' => ['required', 'string', 'max:100'],
            /** Endpoint HTTPS del SaaS que recibe las notificaciones de este afiliado. */
            'notification_url' => ['required', 'url', 'max:500'],
            /** IP o CIDR desde los que se acepta la credencial; vacío = cualquiera. */
            'allowed_ips' => ['sometimes', 'array', 'max:20'],
            'allowed_ips.*' => ['string', 'max:64'],
        ];
    }
}
