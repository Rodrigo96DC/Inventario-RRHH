<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablas = ['funcionarios', 'cargos', 'areas', 'regiones', 'provincias', 'comunas'];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            });
        }
    }

    public function down(): void
    {
        $tablas = ['funcionarios', 'cargos', 'areas', 'regiones', 'provincias', 'comunas'];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
            });
        }
    }
};