<?php

namespace App\Rules;

use App\Enums\DocumentType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Documento en base64: decodificable, dentro del tamaño máximo y con el tipo real permitido
 * (se inspecciona el contenido, no la extensión declarada).
 */
class ValidDocument implements ValidationRule
{
    public function __construct(private DocumentType $type) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $binary = is_string($value) ? base64_decode($value, true) : false;

        if ($binary === false || $binary === '') {
            $fail('El documento :attribute debe venir en base64 válido.');

            return;
        }

        if (strlen($binary) > $this->type->maxBytes()) {
            $fail('El documento :attribute supera el tamaño máximo de '.intdiv($this->type->maxBytes(), 1024 * 1024).' MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);

        if (! in_array($mime, $this->type->allowedMimes(), true)) {
            $fail("Tipo de archivo no permitido en :attribute ({$mime}). Se acepta: ".implode(', ', $this->type->allowedMimes()).'.');
        }
    }
}
