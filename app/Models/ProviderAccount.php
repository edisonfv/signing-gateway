<?php

namespace App\Models;

use App\Casts\VaultEncrypted;
use App\Enums\Environment;
use App\Enums\Provider;
use App\Security\KeyRing;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Cuenta en un proveedor de firma. Las credenciales nunca están en el código:
 * se cargan con `php artisan gateway:provider:credentials` y se guardan cifradas.
 */
#[Fillable(['provider', 'environment', 'name', 'base_url', 'credentials', 'priority', 'is_active'])]
#[Hidden(['credentials'])]
class ProviderAccount extends Model
{
    use HasUuids;

    /**
     * Dónde el proveedor avisa los cambios de una sesión (callbackUrl): la ruta pública de reenvío
     * de la plataforma, que pasa el aviso tal cual al gateway por el túnel. Null si no está configurada.
     * El token impide que cualquiera golpee la ruta con cuentas al azar; solo el gateway lo conoce.
     */
    public function webhookUrl(): ?string
    {
        $base = config('gateway.webhooks.url');

        return filled($base)
            ? rtrim((string) $base, '/')."/{$this->provider->value}/{$this->getKey()}/{$this->webhookToken()}"
            : null;
    }

    /**
     * HMAC del id de la cuenta con la llave de índices ciegos (estable: no se rota), no con APP_KEY.
     */
    public function webhookToken(): string
    {
        return substr(hash_hmac('sha256', "webhook:{$this->getKey()}", app(KeyRing::class)->blindIndexKey()), 0, 40);
    }

    protected function casts(): array
    {
        return [
            'provider' => Provider::class,
            'environment' => Environment::class,
            'credentials' => VaultEncrypted::class.':vault,array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
