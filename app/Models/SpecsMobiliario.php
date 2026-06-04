<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecsMobiliario extends Model
{
    protected $table = 'specs_mobiliario';
    protected $fillable = ['id_item', 'material', 'color', 'dimensiones'];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}