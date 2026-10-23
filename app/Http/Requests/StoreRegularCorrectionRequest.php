<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use App\Rules\ValidDocument;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Corrección de una solicitud de Firmas Regulares en ERROR: el proveedor solo acepta el
 * código dactilar (D02) y las imágenes de la cédula y la selfie (A01, A02, A03).
 */
class StoreRegularCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $images = [DocumentType::identificationFront(), DocumentType::identificationBack(), DocumentType::identificationSelfie()];

        return [
            'finger_code' => ['nullable', 'required_without:documents', 'string', 'size:10', 'alpha_num:ascii'],
            'documents' => ['nullable', 'required_without:finger_code', 'array:'.implode(',', array_map(fn (DocumentType $type) => $type->value, $images))],
            ...collect($images)->mapWithKeys(fn (DocumentType $type) => [
                "documents.{$type->value}" => ['nullable', 'string', new ValidDocument($type)],
            ])->all(),
        ];
    }
}
