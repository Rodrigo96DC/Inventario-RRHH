<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Edificio extends Model
{
    use TieneTrazabilidad;

    protected $table = 'edificios';

    protected $fillable = [
        'nombre', 'direccion', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    public function pisos()
    {
        return $this->hasMany(Piso::class, 'id_edificio');
    }
}