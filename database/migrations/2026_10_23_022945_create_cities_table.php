<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * El nombre solo es único dentro de la provincia: BOLÍVAR, OLMEDO, LA CONCORDIA y
     * PICHINCHA existen en más de una provincia.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('province_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Nombre exacto que espera el proveedor');
            $table->timestamps();

            $table->unique(['province_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
