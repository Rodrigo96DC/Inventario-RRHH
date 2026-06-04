<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'tipo', 'id_ref', 'fecha_ref',
        'titulo', 'mensaje', 'leido',
        'leido_at', 'created_by',
    ];

    protected $casts = [
        'leido'    => 'boolean',
        'fecha_ref'=> 'date',
        'leido_at' => 'datetime',
    ];

    public function scopeNoLeidas($query)
    {
        return $query->where('leido', false);
    }
}