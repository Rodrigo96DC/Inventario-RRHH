<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Piso extends Model
{
    use TieneTrazabilidad;

    protected $table = 'pisos';

    protected $fillable = [
        'id_edificio', 'nombre', 'numero', 'imagen', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'id_edificio');
    }

    public function puestos()
    {
        return $this->hasMany(Puesto::class, 'id_piso');
    }
}