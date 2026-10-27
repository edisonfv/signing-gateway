<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use App\Signing\ProductCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertTenantRequest extends FormRequest
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
        $available = array_map(fn (ProductType $product) => $product->value, app(ProductCatalog::class)->implemented());

        return [
            /** Nombre del afiliado en el SaaS. */
            'name' => ['required', 'string', 'max:255'],
            /** Productos contratados; si se omite, se conservan (al crear: los de por defecto). */
            'products' => ['sometimes', 'array'],
            'products.*' => ['string', Rule::in($available)],
        ];
    }
}
