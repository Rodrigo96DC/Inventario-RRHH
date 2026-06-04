<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TieneTrazabilidad;

class Factura extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'facturas';

    protected $fillable = [
        'id_proveedor', 'numero_factura', 'fecha_factura',
        'monto_total', 'archivo', 'observacion',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['fecha_factura' => 'date'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function items()
    {
        return $this->hasMany(ItemInventario::class, 'id_factura');
    }

    public function evaluacion()
    {
        return $this->hasOne(EvaluacionFactura::class, 'id_factura');
    }
}