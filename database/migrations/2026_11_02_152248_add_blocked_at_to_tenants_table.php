<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Suspendido (suspended_at): comercial; aún puede retirar lo ya emitido.
     * Bloqueado (blocked_at): incidente; nada pasa y sus credenciales se revocan.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('blocked_at')->nullable()->after('suspended_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('blocked_at');
        });
    }
};
