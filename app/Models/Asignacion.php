<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Asignacion extends Model
{
    use TieneTrazabilidad;

    protected $table = 'asignaciones';

    protected $fillable = [
        'id_item', 'id_funcionario', 'cantidad',
        'fecha_asignacion', 'fecha_devolucion',
        'estado', 'observacion',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }
}