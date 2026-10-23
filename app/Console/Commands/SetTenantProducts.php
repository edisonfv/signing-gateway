<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Signing\ProductCatalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:tenant:products {tenant : external_id del tenant} {products?* : Productos contratados, p. ej. HEADLESS REGULAR (vacío = valores por defecto)}')]
#[Description('Define los productos de firma que el tenant contrató (condiciones comerciales)')]
class SetTenantProducts extends Command
{
    public function handle(ProductCatalog $catalog): int
    {
        $tenant = Tenant::where('external_id', $this->argument('tenant'))->firstOrFail();
        $products = array_map('strtoupper', $this->argument('products'));
        $available = array_map(fn (ProductType $product) => $product->value, $catalog->implemented());

        if ($unknown = array_diff($products, $available)) {
            $this->components->error('Productos no disponibles: '.implode(', ', $unknown).'. Disponibles: '.implode(', ', $available).'.');

            return self::FAILURE;
        }

        $tenant->update(['enabled_products' => $products ?: null]);
        AuditLog::record('tenant.products_updated', subject: $tenant, context: ['products' => $products ?: 'default'], ip: 'cli');

        $this->components->info("{$tenant->name}: ".implode(', ', array_map(fn (ProductType $product) => $product->value, $tenant->enabledProducts())));

        return self::SUCCESS;
    }
}
