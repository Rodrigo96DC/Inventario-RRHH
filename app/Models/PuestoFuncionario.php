<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class PuestoFuncionario extends Model
{
    use TieneTrazabilidad;

    protected $table = 'puesto_funcionario';

    protected $fillable = [
        'id_puesto', 'id_funcionario',
        'fecha_inicio', 'fecha_fin',
        'estado', 'observacion',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'id_puesto');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }
}