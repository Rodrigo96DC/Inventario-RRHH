<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;
class Area extends Model
{   use TieneTrazabilidad;
    protected $table = 'areas';
    protected $fillable = ['nombre', 'estado'];

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'id_area');
    }
}