<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TieneTrazabilidad;

class Insumo extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre', 'descripcion', 'unidad_medida',
        'stock_actual', 'stock_minimo', 'metodo_valuacion',
        'estado', 'id_categoria',
        'created_by', 'updated_by', 'deleted_by',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaInventario::class, 'id_categoria');
    }

    public function lotes()
    {
        return $this->hasMany(InsumoLote::class, 'id_insumo');
    }

    public function lotesActivos()
    {
        return $this->hasMany(InsumoLote::class, 'id_insumo')
                    ->where('estado', 'Activo')
                    ->where('cantidad_restante', '>', 0);
    }

    public function movimientos()
    {
        return $this->hasMany(InsumoMovimiento::class, 'id_insumo');
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function getColorStockAttribute(): string
    {
        if ($this->stock_actual <= 0) return 'pill-red';
        if ($this->stock_actual <= $this->stock_minimo) return 'pill-amber';
        return 'pill-green';
    }

    public function creadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}

public function editadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'updated_by');
}
}