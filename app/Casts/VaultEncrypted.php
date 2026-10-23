<?php

namespace App\Casts;

use App\Security\KeyRing;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Cifra un atributo con una llave de la bóveda (no con APP_KEY).
 *
 * Uso: 'certificate' => VaultEncrypted::class, 'password' => VaultEncrypted::class.':vault_password'
 * Con ',array' el valor se serializa como JSON: VaultEncrypted::class.':vault,array'
 *
 * @implements CastsAttributes<mixed, mixed>
 */
class VaultEncrypted implements CastsAttributes
{
    public function __construct(
        private string $purpose = KeyRing::VAULT,
        private ?string $format = null,
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        $plain = app(KeyRing::class)->encrypter($this->purpose)->decryptString($value);

        return $this->format === 'array' ? json_decode($plain, true, flags: JSON_THROW_ON_ERROR) : $plain;
    }

    public function set(Model $model, string $key, #[\SensitiveParameter] mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $plain = $this->format === 'array' ? json_encode($value, JSON_THROW_ON_ERROR) : (string) $value;

        return app(KeyRing::class)->encrypter($this->purpose)->encryptString($plain);
    }
}
