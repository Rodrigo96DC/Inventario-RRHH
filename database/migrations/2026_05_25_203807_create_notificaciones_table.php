<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->unsignedBigInteger('id_ref')->nullable();
            $table->date('fecha_ref')->nullable();
            $table->string('titulo');
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};