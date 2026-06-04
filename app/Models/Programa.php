<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Programa extends Model
{
    use TieneTrazabilidad;

    protected $table = 'programas';

    protected $fillable = [
        'nombre', 'grupo', 'version', 'fecha',
        'link', 'archivo', 'es_basico', 'estado', 'observacion',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'es_basico' => 'boolean',
    ];

    public function items()
    {
        return $this->belongsToMany(ItemInventario::class, 'item_programas', 'id_programa', 'id_item')
                    ->withPivot('fecha_instalacion')
                    ->withTimestamps();
    }
}