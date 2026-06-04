<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumoLote extends Model
{
    protected $table = 'insumo_lotes';

    protected $fillable = [
        'id_insumo', 'id_factura',
        'cantidad_inicial', 'cantidad_restante',
        'fecha_ingreso', 'fecha_vencimiento',
        'precio_unitario', 'estado', 'created_by',
    ];

    protected $casts = [
        'fecha_ingreso'     => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }
}