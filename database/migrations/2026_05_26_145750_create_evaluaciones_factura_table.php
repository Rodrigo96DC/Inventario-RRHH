<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_factura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_factura')->unique()->constrained('facturas')->cascadeOnDelete();
            $table->unsignedTinyInteger('calificacion'); // 1 a 5
            $table->text('comentario')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_factura');
    }
};