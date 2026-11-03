<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Última señal de vida de cada pieza (scheduler, cada cola, reconciliación, proveedor…), para el
     * monitor de salud. Una fila por pieza; en la base y no en la caché, para sobrevivir a cache:clear.
     */
    public function up(): void
    {
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->string('name', 100)->primary();
            $table->timestamp('beat_at');
            $table->json('meta')->nullable()->comment('Detalle de la última señal; nunca datos de titulares');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heartbeats');
    }
};
