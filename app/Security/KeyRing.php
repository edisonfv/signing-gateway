<?php

namespace App\Security;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Entrega los cifradores de la bóveda a partir de las llaves de config/gateway.php.
 *
 * Cada propósito usa su propia llave AES-256-GCM, distinta de APP_KEY, para que el
 * robo de una no exponga lo protegido por las otras.
 */
class KeyRing
{
    public const VAULT = 'vault';

    public const VAULT_PASSWORD = 'vault_password';

    private const CIPHER = 'aes-256-gcm';

    /** @var array<string, Encrypter> */
    private array $encrypters = [];

    public function encrypter(string $purpose): Encrypter
    {
        return $this->encrypters[$purpose] ??= $this->makeEncrypter($purpose);
    }

    public function blindIndexKey(): string
    {
        return $this->decodeKey(config('gateway.keys.blind_index'), 'blind_index');
    }

    public static function generateKey(): string
    {
        return 'base64:'.base64_encode(Encrypter::generateKey(self::CIPHER));
    }

    private function makeEncrypter(string $purpose): Encrypter
    {
        if (! in_array($purpose, [self::VAULT, self::VAULT_PASSWORD], true)) {
            throw new RuntimeException("Propósito de cifrado desconocido [{$purpose}].");
        }

        $encrypter = new Encrypter($this->decodeKey(config("gateway.keys.{$purpose}"), $purpose), self::CIPHER);

        return $encrypter->previousKeys(array_map(
            fn (string $key) => $this->decodeKey($key, "{$purpose}_previous"),
            config("gateway.keys.{$purpose}_previous", []),
        ));
    }

    private function decodeKey(#[\SensitiveParameter] ?string $key, string $name): string
    {
        if (blank($key)) {
            throw new RuntimeException("Falta la llave [gateway.keys.{$name}]. Ejecute `php artisan gateway:keys:generate`.");
        }

        $decoded = Str::startsWith($key, 'base64:') ? base64_decode(Str::after($key, 'base64:'), true) : $key;

        if ($decoded === false || mb_strlen($decoded, '8bit') !== 32) {
            throw new RuntimeException("La llave [gateway.keys.{$name}] debe tener 32 bytes.");
        }

        return $decoded;
    }
}
