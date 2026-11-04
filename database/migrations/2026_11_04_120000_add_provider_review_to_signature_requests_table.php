<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Firmas Regulares: lo que el proveedor cuenta mientras revisa (operador asignado, observaciones y su
     * última actualización), para que el SaaS lo muestre. Sin datos del titular: las cédulas van tapadas.
     */
    public function up(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->json('provider_review')->nullable()->after('provider_stage');
        });
    }

    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropColumn('provider_review');
        });
    }
};
