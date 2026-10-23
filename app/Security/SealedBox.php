<?php

namespace App\Security;

use InvalidArgumentException;

/**
 * Sella datos con la llave pública X25519 del cliente (libsodium sealed box).
 * Solo quien tiene la llave privada (el SaaS) puede abrirlos; el gateway no.
 */
class SealedBox
{
    public static function seal(#[\SensitiveParameter] string $plain, string $base64PublicKey): string
    {
        $publicKey = base64_decode($base64PublicKey, true);

        if ($publicKey === false || strlen($publicKey) !== SODIUM_CRYPTO_BOX_PUBLICKEYBYTES) {
            throw new InvalidArgumentException('La llave pública de entrega no es válida.');
        }

        return base64_encode(sodium_crypto_box_seal($plain, $publicKey));
    }

    public static function isValidPublicKey(?string $base64PublicKey): bool
    {
        $publicKey = base64_decode((string) $base64PublicKey, true);

        return $publicKey !== false && strlen($publicKey) === SODIUM_CRYPTO_BOX_PUBLICKEYBYTES;
    }
}
