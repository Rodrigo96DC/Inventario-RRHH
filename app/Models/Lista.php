<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TieneTrazabilidad;

class Lista extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'listas';

  protected $fillable = [
    'nombre', 'descripcion', 'tipo', 'estado',
    'visibilidad', 'id_creador',
    'created_by', 'updated_by', 'deleted_by',
];

    public function items()
    {
        return $this->hasMany(ListaItem::class, 'id_lista');
    }
}