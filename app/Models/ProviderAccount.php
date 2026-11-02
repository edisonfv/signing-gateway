<?php

namespace App\Models;

use App\Casts\VaultEncrypted;
use App\Enums\Environment;
use App\Enums\Provider;
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
     * Dónde el proveedor avisa los cambios de una sesión (callbackUrl), o null si el gateway
     * no tiene URL pública. El token impide que cualquiera golpee la ruta con cuentas al azar.
     */
    public function webhookUrl(): ?string
    {
        $base = config('gateway.webhooks.url');

        return filled($base)
            ? rtrim((string) $base, '/')."/webhooks/{$this->provider->value}/{$this->getKey()}/{$this->webhookToken()}"
            : null;
    }

    public function webhookToken(): string
    {
        return substr(hash_hmac('sha256', "webhook:{$this->getKey()}", (string) config('app.key')), 0, 40);
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
