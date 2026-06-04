<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_pisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_piso')->constrained('pisos')->cascadeOnDelete();
            $table->json('snapshot_puestos');   // puestos que existían
            $table->json('snapshot_asignaciones'); // asignaciones activas
            $table->string('motivo')->nullable();
            $table->boolean('revertido')->default(false);
            $table->timestamp('revertido_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('revertido_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_pisos');
    }
};