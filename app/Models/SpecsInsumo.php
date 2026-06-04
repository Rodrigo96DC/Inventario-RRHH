<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecsInsumo extends Model
{
    protected $table = 'specs_insumo';
    protected $fillable = ['id_item', 'stock_inicial', 'stock_actual', 'unidad_medida', 'stock_minimo'];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}