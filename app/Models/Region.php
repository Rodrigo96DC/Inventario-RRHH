<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';

    protected $fillable = ['numero', 'nombre', 'capital'];

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'id_region');
    }

    public function provincias()
    {
        return $this->hasMany(Provincia::class, 'id_region');
    }
}
