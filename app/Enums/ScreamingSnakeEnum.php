<?php

namespace App\Enums;

use Closure;
use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * Base para enums cuyo valor es el nombre del método en SCREAMING_SNAKE_CASE
 * (p. ej. naturalPersonRuc() => "NATURAL_PERSON_RUC"), que es la convención del proveedor.
 */
abstract class ScreamingSnakeEnum extends Enum
{
    protected static function values(): Closure
    {
        return fn (string $method): string => Str::upper(Str::snake($method));
    }
}
