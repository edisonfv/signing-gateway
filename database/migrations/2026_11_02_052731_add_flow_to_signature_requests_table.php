<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flujo con el que el proveedor ejecuta la solicitud: el gateway la conduce según él, no según el producto.
     */
    public function up(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->string('flow', 30)->nullable()->after('product');
        });

        DB::table('signature_requests')->where('product', 'HEADLESS')->update(['flow' => 'LIVENESS_API']);
        DB::table('signature_requests')->where('product', 'REGULAR')->update(['flow' => 'DOCUMENT_REVIEW']);

        Schema::table('signature_requests', function (Blueprint $table) {
            $table->string('flow', 30)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropColumn('flow');
        });
    }
};
