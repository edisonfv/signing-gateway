<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Cédula ecuatoriana: 10 dígitos, provincia 01–24 o 30, tercer dígito < 6 y
 * dígito verificador por módulo 10.
 */
class EcuadorianIdentification implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::isValid($value, config('gateway.strict_identification'))) {
            $fail('El campo :attribute no es una cédula ecuatoriana válida.');
        }
    }

    /**
     * Con $strict = false solo se exigen 10 dígitos (datos de prueba del sandbox).
     */
    public static function isValid(string $identification, bool $strict = true): bool
    {
        if (preg_match('/^\d{10}$/', $identification) !== 1) {
            return false;
        }

        if (! $strict) {
            return true;
        }

        $province = (int) substr($identification, 0, 2);

        if (! (($province >= 1 && $province <= 24) || $province === 30) || (int) $identification[2] >= 6) {
            return false;
        }

        $sum = 0;

        foreach (str_split(substr($identification, 0, 9)) as $index => $digit) {
            $product = (int) $digit * ($index % 2 === 0 ? 2 : 1);
            $sum += $product > 9 ? $product - 9 : $product;
        }

        return (10 - ($sum % 10)) % 10 === (int) $identification[9];
    }
}
