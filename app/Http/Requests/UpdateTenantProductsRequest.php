<?php

namespace App\Http\Requests;

use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Models\ProviderAccount;
use App\Signing\ProductCatalog;
use App\Signing\Routing\ProviderRouter;
use App\Signing\SignatureProviderManager;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Productos que contrata un afiliado y sus condiciones. Reemplaza la configuración completa:
 * los productos que no se envían quedan desactivados.
 */
class UpdateTenantProductsRequest extends FormRequest
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
            /** Productos contratados. Vacío = el afiliado no puede emitir. */
            'products' => ['present', 'array'],
            'products.*.product' => ['required', 'string', Rule::in($available), 'distinct'],
            /** Vigencias que puede vender (p. ej. `ONE_YEAR`). Se omite o null = todas las que ofrece el proveedor. */
            'products.*.periods' => ['nullable', 'array', 'min:1'],
            'products.*.periods.*' => ['string', Rule::in(CertificatePeriod::toValues()), 'distinct'],
            /** Cuenta de proveedor que atiende al afiliado (ver GET /v1/admin/products). Null = por prioridad. */
            'products.*.preferred_provider_account_id' => ['nullable', 'uuid', 'exists:provider_accounts,id'],
        ];
    }

    /**
     * Las vigencias y la cuenta deben corresponder a lo que el proveedor realmente ofrece.
     *
     * @return array<int, Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                foreach ($this->input('products') as $index => $item) {
                    $this->validateAgainstOffering($validator, $index, $item);
                }
            },
        ];
    }

    /**
     * @param  array{product: string, periods?: array<int, string>|null, preferred_provider_account_id?: string|null}  $item
     */
    private function validateAgainstOffering(Validator $validator, int $index, array $item): void
    {
        $product = ProductType::from($item['product']);
        $candidates = app(ProviderRouter::class)->candidates($product);
        $accountId = $item['preferred_provider_account_id'] ?? null;
        $account = $accountId ? $candidates->firstWhere('id', $accountId) : $candidates->first();

        if ($accountId && $account === null) {
            $validator->errors()->add("products.{$index}.preferred_provider_account_id", 'La cuenta no está activa en este ambiente o no ofrece el producto.');

            return;
        }

        if ($account === null || empty($item['periods'])) {
            return;
        }

        /** @var ProviderAccount $account */
        $offering = app(SignatureProviderManager::class)->forAccount($account)->offering($product);
        $offered = array_map(fn (CertificatePeriod $period) => $period->value, $offering->periods);

        if ($unknown = array_diff($item['periods'], $offered)) {
            $validator->errors()->add("products.{$index}.periods", 'Vigencias no ofrecidas por el proveedor: '.implode(', ', $unknown).'.');
        }
    }
}
