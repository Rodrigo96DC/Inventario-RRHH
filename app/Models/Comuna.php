<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    protected $table = 'comunas';
    protected $fillable = ['id_provincia', 'nombre'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'id_comuna');
    }
}