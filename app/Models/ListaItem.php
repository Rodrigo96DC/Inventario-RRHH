<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaItem extends Model
{
    protected $table = 'lista_items';

    protected $fillable = [
        'id_lista', 'id_item', 'id_funcionario',
        'comentario', 'completado',
    ];

    protected $casts = ['completado' => 'boolean'];

    public function lista()
    {
        return $this->belongsTo(Lista::class, 'id_lista');
    }

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }
}