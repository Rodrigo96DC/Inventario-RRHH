<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumoMovimiento extends Model
{
    protected $table = 'insumo_movimientos';

    protected $fillable = [
        'id_insumo', 'id_lote', 'id_factura', 'id_funcionario',
        'tipo', 'cantidad', 'fecha', 'observacion', 'created_by',
    ];

    protected $casts = ['fecha' => 'date'];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }

    public function lote()
    {
        return $this->belongsTo(InsumoLote::class, 'id_lote');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}