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
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->timestamp('last_reconciled_at')->nullable()->after('closed_at')->comment('Última consulta de tracking al proveedor');
            $table->index(['status', 'last_reconciled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'last_reconciled_at']);
            $table->dropColumn('last_reconciled_at');
        });
    }
};
