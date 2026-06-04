<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialPiso extends Model
{
    protected $table = 'historial_pisos';

    protected $fillable = [
        'id_piso', 'snapshot_puestos', 'snapshot_asignaciones',
        'motivo', 'revertido', 'revertido_at',
        'created_by', 'revertido_by',
    ];

    protected $casts = [
        'snapshot_puestos'      => 'array',
        'snapshot_asignaciones' => 'array',
        'revertido'             => 'boolean',
        'revertido_at'          => 'datetime',
    ];

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'id_piso');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revertidoPor()
    {
        return $this->belongsTo(User::class, 'revertido_by');
    }
}