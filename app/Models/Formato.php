<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Formato extends Model
{
    use TieneTrazabilidad;

    protected $table = 'formatos';

    protected $fillable = [
        'id_item', 'fecha_formato',
        'programas_anteriores', 'observacion', 'created_by',
    ];

    protected $casts = [
        'fecha_formato'       => 'date',
        'programas_anteriores' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}