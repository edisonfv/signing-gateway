<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los datos del titular se borran tras la retención (gateway.retention.applicant_data_days);
     * la solicitud queda con su estado, producto e índice ciego.
     */
    public function up(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->text('applicant')->nullable()->comment('Datos del titular (encrypted:array); null tras la purga')->change();
            $table->timestamp('applicant_purged_at')->nullable()->after('closed_at');
            $table->index(['closed_at', 'applicant_purged_at']);
        });
    }

    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropIndex(['closed_at', 'applicant_purged_at']);
            $table->dropColumn('applicant_purged_at');
        });
    }
};
