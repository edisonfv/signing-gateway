<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Códigos de emparejamiento de la plataforma: de un solo uso, de vida corta y guardados como hash.
     */
    public function up(): void
    {
        Schema::create('pairing_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('code_hash', 64)->unique()->comment('SHA-256 del código normalizado; el código nunca se guarda');
            $table->timestamp('expires_at');
            $table->unsignedSmallInteger('failed_attempts')->default(0);
            $table->timestamp('used_at')->nullable();
            $table->string('used_ip', 45)->nullable();
            $table->foreignUuid('api_client_id')->nullable()->constrained()->nullOnDelete()->comment('Credencial emitida al usarlo');
            $table->timestamps();

            $table->index(['used_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairing_codes');
    }
};
