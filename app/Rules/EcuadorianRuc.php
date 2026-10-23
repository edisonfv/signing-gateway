<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * RUC de persona natural: cédula válida seguida de "001" (13 dígitos), como exige el proveedor.
 */
class EcuadorianRuc implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)
            || preg_match('/^\d{10}001$/', $value) !== 1
            || ! EcuadorianIdentification::isValid(substr($value, 0, 10), config('gateway.strict_identification'))) {
            $fail('El campo :attribute debe ser un RUC de persona natural válido (cédula + 001).');
        }
    }
}
