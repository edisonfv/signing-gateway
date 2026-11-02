<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro de emisiones: una fila por certificado que emitió el proveedor. Solo se insertan
     * filas (un trigger rechaza UPDATE y DELETE) y cada una encadena el hash de la anterior.
     * No guarda datos personales: sobrevive a la purga del titular.
     */
    public function up(): void
    {
        Schema::create('issuance_ledger', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('sequence')->unique()->comment('Correlativo sin huecos: un hueco delata una fila borrada');
            $table->foreignUuid('signature_request_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('api_client_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('provider_account_id')->constrained()->restrictOnDelete();
            $table->string('provider', 50);
            $table->string('environment', 20);
            $table->string('product', 30);
            $table->string('flow', 30);
            $table->string('application_type', 30);
            $table->string('period', 20);
            $table->string('provider_reference')->nullable()->comment('sessionId (Flujo por API) o requestId (Firmas Regulares) del proveedor');
            $table->string('serial_number')->nullable();
            $table->char('identification_hash', 64)->comment('Índice ciego de la cédula');
            $table->timestamp('issued_at');
            $table->string('source', 20)->comment('job, reconcile, webhook o backfill');
            $table->char('previous_hash', 64)->nullable();
            $table->char('hash', 64);
            $table->timestamp('created_at');

            $table->index(['tenant_id', 'issued_at']);
            $table->index(['provider_account_id', 'issued_at']);
            $table->index('issued_at');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared(<<<'SQL'
                CREATE OR REPLACE FUNCTION issuance_ledger_append_only() RETURNS trigger AS $$
                BEGIN
                    RAISE EXCEPTION 'issuance_ledger solo admite inserciones';
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER issuance_ledger_append_only
                    BEFORE UPDATE OR DELETE ON issuance_ledger
                    FOR EACH ROW EXECUTE FUNCTION issuance_ledger_append_only();
                SQL);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('issuance_ledger');

        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared('DROP FUNCTION IF EXISTS issuance_ledger_append_only()');
        }
    }
};
