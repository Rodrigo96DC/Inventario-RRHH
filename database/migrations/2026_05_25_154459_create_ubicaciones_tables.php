<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Edificios
        Schema::create('edificios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Pisos
        Schema::create('pisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_edificio')->constrained('edificios')->cascadeOnDelete();
            $table->string('nombre');
            $table->integer('numero');
            $table->string('imagen')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Puestos
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_piso')->constrained('pisos')->cascadeOnDelete();
            $table->string('numero');
            $table->string('seccion')->nullable();
            $table->decimal('x1', 8, 2);
            $table->decimal('y1', 8, 2);
            $table->decimal('x2', 8, 2);
            $table->decimal('y2', 8, 2);
            $table->enum('estado', ['Disponible', 'Ocupado', 'Inhabilitado'])->default('Disponible');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Asignación puesto → funcionario
        Schema::create('puesto_funcionario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_puesto')->constrained('puestos')->cascadeOnDelete();
            $table->foreignId('id_funcionario')->constrained('funcionarios')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['Activo', 'Liberado'])->default('Activo');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puesto_funcionario');
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('pisos');
        Schema::dropIfExists('edificios');
    }
};