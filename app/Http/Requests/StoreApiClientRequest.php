<?php

namespace App\Http\Requests;

use App\Security\NotificationTarget;
use Closure;
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
            /** Endpoint HTTPS de la plataforma que recibe las notificaciones; su host debe estar en GATEWAY_NOTIFICATION_HOSTS. */
            'notification_url' => ['required', 'url:https', 'max:500', function (string $attribute, mixed $value, Closure $fail) {
                if (! NotificationTarget::isAllowed((string) $value)) {
                    $fail('El host de la URL de notificaciones no está permitido en el gateway (GATEWAY_NOTIFICATION_HOSTS).');
                }
            }],
            /** IP o CIDR desde los que se acepta la credencial; vacío = cualquiera. */
            'allowed_ips' => ['sometimes', 'array', 'max:20'],
            'allowed_ips.*' => ['string', 'max:64', function (string $attribute, mixed $value, Closure $fail) {
                [$ip, $prefix] = array_pad(explode('/', (string) $value, 2), 2, null);
                $maxPrefix = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 128 : 32;

                if (! filter_var($ip, FILTER_VALIDATE_IP) || ($prefix !== null && (! ctype_digit($prefix) || (int) $prefix > $maxPrefix))) {
                    $fail('Cada IP permitida debe ser una IP o un CIDR (p. ej. 10.8.0.2 o 10.8.0.0/24).');
                }
            }],
        ];
    }
}
