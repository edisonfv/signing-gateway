<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('enabled_products')->nullable()->after('hourly_cap')->comment('Productos contratados; null usa gateway.default_products');
        });

        Schema::table('signature_requests', function (Blueprint $table) {
            $table->string('provider_request_id')->nullable()->index()->after('provider_session_id')->comment('requestId del proveedor (Firmas Regulares)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropColumn('provider_request_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('enabled_products');
        });
    }
};
