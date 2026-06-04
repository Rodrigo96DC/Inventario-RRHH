<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class Merma extends Model
{
    use TieneTrazabilidad;

    protected $table = 'mermas';

    protected $fillable = [
        'id_item', 'cantidad', 'descripcion',
        'lugar_guardado', 'fecha_merma',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['fecha_merma' => 'date'];

    public function item()
    {
        return $this->belongsTo(ItemInventario::class, 'id_item');
    }
}