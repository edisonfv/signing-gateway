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
