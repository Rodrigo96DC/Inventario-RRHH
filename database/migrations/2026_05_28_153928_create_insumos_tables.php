<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de insumos
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('unidad_medida'); // unidad, caja, resma, etc.
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->enum('metodo_valuacion', ['FIFO', 'LIFO'])->default('FIFO');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->foreignId('id_categoria')->nullable()->constrained('categorias_inventario')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // Lotes de insumos (para FIFO/LIFO)
        Schema::create('insumo_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_insumo')->constrained('insumos')->cascadeOnDelete();
            $table->foreignId('id_factura')->nullable()->constrained('facturas')->nullOnDelete();
            $table->integer('cantidad_inicial');
            $table->integer('cantidad_restante');
            $table->date('fecha_ingreso');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->enum('estado', ['Activo', 'Agotado'])->default('Activo');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
        });

        // Movimientos de insumos
        Schema::create('insumo_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_insumo')->constrained('insumos')->cascadeOnDelete();
            $table->foreignId('id_lote')->nullable()->constrained('insumo_lotes')->nullOnDelete();
            $table->foreignId('id_factura')->nullable()->constrained('facturas')->nullOnDelete();
            $table->foreignId('id_funcionario')->nullable()->constrained('funcionarios')->nullOnDelete();
            $table->enum('tipo', ['Entrada', 'Salida']);
            $table->integer('cantidad');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumo_movimientos');
        Schema::dropIfExists('insumo_lotes');
        Schema::dropIfExists('insumos');
    }
};