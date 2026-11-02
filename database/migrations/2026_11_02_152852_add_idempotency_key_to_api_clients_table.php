<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Idempotency-Key con la que la plataforma pidió la credencial: si reintenta con la misma clave,
     * la credencial anterior se revoca en vez de quedar huérfana y activa.
     */
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->string('idempotency_key', 100)->nullable()->after('name');
            $table->index(['tenant_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'idempotency_key']);
            $table->dropColumn('idempotency_key');
        });
    }
};
