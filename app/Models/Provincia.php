<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';
    protected $fillable = ['id_region', 'nombre'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }

    public function comunas()
    {
        return $this->hasMany(Comuna::class, 'id_provincia');
    }
}