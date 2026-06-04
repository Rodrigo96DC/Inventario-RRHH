<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class CategoriaInventario extends Model
{
    use TieneTrazabilidad;

    protected $table = 'categorias_inventario';

    protected $fillable = ['nombre', 'icono', 'estado', 'created_by', 'updated_by', 'deleted_by'];

  

    public function itemsDirectos()
{
    return $this->hasMany(ItemInventario::class, 'id_categoria');
}

public function items()
{
    return $this->belongsToMany(
        ItemInventario::class,
        'item_categorias',
        'id_categoria',
        'id_item'
    )->withPivot('es_principal');
}
}