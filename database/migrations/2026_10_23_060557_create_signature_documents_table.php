<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Documentos de Firmas Regulares: el contenido se guarda cifrado con la llave de la bóveda
     * en el disco "documents" solo hasta enviarlo al proveedor; luego se purga.
     */
    public function up(): void
    {
        Schema::create('signature_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('signature_request_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('mime', 50);
            $table->unsignedInteger('size');
            $table->char('sha256', 64);
            $table->string('path')->nullable();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();

            $table->unique(['signature_request_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_documents');
    }
};
