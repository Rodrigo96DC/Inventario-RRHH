<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->date('fecha_fin_contrato')->nullable()->after('fecha_ingreso');
            $table->integer('dias_aviso_contrato')->default(30)->after('fecha_fin_contrato');
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn(['fecha_fin_contrato', 'dias_aviso_contrato']);
        });
    }
};