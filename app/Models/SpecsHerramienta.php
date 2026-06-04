<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecsHerramienta extends Model
{
    protected $table = 'specs_herramienta';
    protected $fillable = ['id_item', 'tipo', 'material', 'medida'];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}