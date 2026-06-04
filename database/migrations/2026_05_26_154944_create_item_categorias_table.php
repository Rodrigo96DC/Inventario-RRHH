<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_item')->constrained('items_inventario')->cascadeOnDelete();
            $table->foreignId('id_categoria')->constrained('categorias_inventario')->cascadeOnDelete();
            $table->boolean('es_principal')->default(false); // categoría principal del ítem
            $table->timestamps();
            $table->unique(['id_item', 'id_categoria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_categorias');
    }
};