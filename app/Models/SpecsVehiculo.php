<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecsVehiculo extends Model
{
    protected $table = 'specs_vehiculo';
    protected $fillable = [
        'id_item', 'patente', 'anio', 'tipo_combustible',
        'numero_motor', 'numero_chasis', 'kilometraje',
    ];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}