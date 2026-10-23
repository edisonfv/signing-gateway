<?php

namespace App\Security;

use Illuminate\Support\Str;

/**
 * Índice ciego: HMAC-SHA256 de un valor normalizado.
 *
 * Permite buscar o detectar duplicados por cédula, email, etc. sin guardarlos en claro,
 * ya que los campos con cast `encrypted` no se pueden consultar.
 */
class BlindIndex
{
    public function __construct(private KeyRing $keys) {}

    public function for(string $field, #[\SensitiveParameter] string $value): string
    {
        $normalized = Str::of($value)->trim()->upper()->replaceMatches('/\s+/', ' ')->toString();

        return hash_hmac('sha256', "{$field}|{$normalized}", $this->keys->blindIndexKey());
    }
}
