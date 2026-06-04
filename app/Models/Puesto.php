<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Puesto extends Model
{
    use TieneTrazabilidad;

    protected $table = 'puestos';

    protected $fillable = [
        'id_piso', 'numero', 'seccion',
        'x1', 'y1', 'x2', 'y2', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'id_piso');
    }

    public function asignaciones()
    {
        return $this->hasMany(PuestoFuncionario::class, 'id_puesto');
    }

    public function asignacionActiva()
    {
        return $this->hasOne(PuestoFuncionario::class, 'id_puesto')
                    ->where('estado', 'Activo')
                    ->latest();
    }

    public function funcionarioActual()
    {
        return $this->hasOneThrough(
            Funcionario::class,
            PuestoFuncionario::class,
            'id_puesto',
            'id',
            'id',
            'id_funcionario'
        )->where('puesto_funcionario.estado', 'Activo');
    }
}