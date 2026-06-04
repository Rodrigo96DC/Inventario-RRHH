<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Regiones
        Schema::create('regiones', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->string('nombre');
            $table->string('capital');
            $table->timestamps();
        });

        // Provincias
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_region')->constrained('regiones')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
        });

        // Comunas
        Schema::create('comunas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_provincia')->constrained('provincias')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
        });

        // Cargos
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('grado')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });

        // Áreas
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });

        // Funcionarios
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_region')->nullable()->constrained('regiones')->nullOnDelete();
            $table->foreignId('id_provincia')->nullable()->constrained('provincias')->nullOnDelete();
            $table->foreignId('id_comuna')->nullable()->constrained('comunas')->nullOnDelete();
            $table->foreignId('id_cargo')->nullable()->constrained('cargos')->nullOnDelete();
            $table->foreignId('id_area')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('rut')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('tipo_contrato', ['Planta', 'Contrata', 'Honorarios'])->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo', 'Licencia', 'Vacaciones'])->default('Activo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('comunas');
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('regiones');
    }
};