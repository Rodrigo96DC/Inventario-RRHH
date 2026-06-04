<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecsComputacional extends Model
{
    protected $table = 'specs_computacional';

    protected $fillable = [
        'id_item', 'procesador', 'ram', 'tipo_disco',
        'capacidad_disco', 'mac', 'ip', 'hostname',
        'numero_inventario', 'entradas',
    ];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}