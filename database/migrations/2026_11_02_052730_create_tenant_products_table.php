<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Productos contratados por tenant, uno por fila, con sus condiciones. Reemplaza tenants.enabled_products.
     */
    public function up(): void
    {
        Schema::create('tenant_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('product', 30);
            $table->json('periods')->nullable()->comment('Vigencias contratadas; null = todas las que ofrece el proveedor');
            $table->foreignUuid('preferred_provider_account_id')->nullable()->constrained('provider_accounts')->nullOnDelete()->comment('Cuenta que atiende al tenant; si no está disponible se usa la prioridad');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'product']);
        });

        DB::table('tenants')->whereNotNull('enabled_products')->orderBy('id')->each(function (object $tenant) {
            foreach (array_unique(json_decode($tenant->enabled_products, true) ?: []) as $product) {
                DB::table('tenant_products')->insert([
                    'id' => (string) Str::uuid7(),
                    'tenant_id' => $tenant->id,
                    'product' => $product,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('enabled_products');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('enabled_products')->nullable()->after('hourly_cap')->comment('Productos contratados; null usa gateway.default_products');
        });

        DB::table('tenant_products')->where('is_active', true)->get()->groupBy('tenant_id')->each(
            fn ($products, string $tenantId) => DB::table('tenants')->where('id', $tenantId)->update(['enabled_products' => json_encode($products->pluck('product')->values())]),
        );

        Schema::dropIfExists('tenant_products');
    }
};
