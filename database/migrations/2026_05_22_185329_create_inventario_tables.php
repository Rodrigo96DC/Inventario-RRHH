<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorías de inventario
        Schema::create('categorias_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('icono')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Proveedores
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('rut')->unique();
            $table->string('contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Facturas
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proveedor')->constrained('proveedores')->cascadeOnDelete();
            $table->string('numero_factura')->unique();
            $table->date('fecha_factura');
            $table->decimal('monto_total', 12, 2)->nullable();
            $table->string('archivo')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Items de inventario (tabla base para todos los tipos)
        Schema::create('items_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_categoria')->constrained('categorias_inventario')->cascadeOnDelete();
            $table->foreignId('id_proveedor')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignId('id_factura')->nullable()->constrained('facturas')->nullOnDelete();
            $table->string('nombre');
            $table->string('codigo_interno')->unique()->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->decimal('precio', 12, 2)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('ubicacion')->nullable();
            $table->text('observacion')->nullable();
            $table->enum('estado', [
                'Disponible',
                'Asignado',
                'En mantención',
                'De baja',
                'Extraviado'
            ])->default('Disponible');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Specs computacional
        Schema::create('specs_computacional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->unique()->constrained('items_inventario')->cascadeOnDelete();
            $table->string('procesador')->nullable();
            $table->string('ram')->nullable();
            $table->string('tipo_disco')->nullable();
            $table->string('capacidad_disco')->nullable();
            $table->string('mac')->nullable();
            $table->string('ip')->nullable();
            $table->string('hostname')->nullable();
            $table->string('numero_inventario')->nullable();
            $table->string('entradas')->nullable();
            $table->timestamps();
        });

        // Specs mobiliario
        Schema::create('specs_mobiliario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->unique()->constrained('items_inventario')->cascadeOnDelete();
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('dimensiones')->nullable();
            $table->timestamps();
        });

        // Specs vehículo
        Schema::create('specs_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->unique()->constrained('items_inventario')->cascadeOnDelete();
            $table->string('patente')->nullable();
            $table->year('anio')->nullable();
            $table->string('tipo_combustible')->nullable();
            $table->string('numero_motor')->nullable();
            $table->string('numero_chasis')->nullable();
            $table->integer('kilometraje')->nullable();
            $table->timestamps();
        });

        // Specs herramienta
        Schema::create('specs_herramienta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->unique()->constrained('items_inventario')->cascadeOnDelete();
            $table->string('tipo')->nullable();
            $table->string('material')->nullable();
            $table->string('medida')->nullable();
            $table->timestamps();
        });

        // Specs insumo/consumible
        Schema::create('specs_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->unique()->constrained('items_inventario')->cascadeOnDelete();
            $table->integer('stock_inicial')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->string('unidad_medida')->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->timestamps();
        });

        // Asignaciones (tabla única para todos los tipos)
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->constrained('items_inventario')->cascadeOnDelete();
            $table->foreignId('id_funcionario')->constrained('funcionarios')->cascadeOnDelete();
            $table->integer('cantidad')->default(1);
            $table->date('fecha_asignacion');
            $table->date('fecha_devolucion')->nullable();
            $table->enum('estado', ['Activo', 'Devuelto'])->default('Activo');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Mermas
        Schema::create('mermas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->constrained('items_inventario')->cascadeOnDelete();
            $table->integer('cantidad')->default(1);
            $table->text('descripcion');
            $table->string('lugar_guardado')->nullable();
            $table->date('fecha_merma');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Programas
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('grupo')->nullable();
            $table->string('version')->nullable();
            $table->date('fecha')->nullable();
            $table->string('link')->nullable();
            $table->string('archivo')->nullable();
            $table->boolean('es_basico')->default(false);
            $table->enum('estado', ['Instalar', 'No instalar'])->default('Instalar');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Programas instalados por equipo
        Schema::create('item_programas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->constrained('items_inventario')->cascadeOnDelete();
            $table->foreignId('id_programa')->constrained('programas')->cascadeOnDelete();
            $table->date('fecha_instalacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
        });

        // Formatos (historial de formateos)
        Schema::create('formatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->constrained('items_inventario')->cascadeOnDelete();
            $table->date('fecha_formato');
            $table->json('programas_anteriores')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
        });

        // Listas
        Schema::create('listas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['Equipos', 'Funcionarios'])->default('Equipos');
            $table->enum('estado', ['Pendiente', 'En proceso', 'Completado'])->default('Pendiente');
                $table->enum('visibilidad', ['Publica', 'Privada'])->default('Privada');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Items de listas
        Schema::create('lista_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lista')->constrained('listas')->cascadeOnDelete();
            $table->foreignId('id_item')->nullable()->constrained('items_inventario')->nullOnDelete();
            $table->foreignId('id_funcionario')->nullable()->constrained('funcionarios')->nullOnDelete();
            $table->text('comentario')->nullable();
            $table->boolean('completado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_items');
        Schema::dropIfExists('listas');
        Schema::dropIfExists('formatos');
        Schema::dropIfExists('item_programas');
        Schema::dropIfExists('programas');
        Schema::dropIfExists('mermas');
        Schema::dropIfExists('asignaciones');
        Schema::dropIfExists('specs_insumo');
        Schema::dropIfExists('specs_herramienta');
        Schema::dropIfExists('specs_vehiculo');
        Schema::dropIfExists('specs_mobiliario');
        Schema::dropIfExists('specs_computacional');
        Schema::dropIfExists('items_inventario');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('categorias_inventario');
    }
};