<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Cargo extends Model
{
    use TieneTrazabilidad;
    protected $table = 'cargos';
    protected $fillable = ['nombre', 'grado', 'estado'];

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'id_cargo');
    }
}