<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TieneTrazabilidad;


class ItemInventario extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'items_inventario';

    protected $fillable = [
        'id_categoria', 'id_proveedor', 'id_factura',
        'nombre', 'codigo_interno', 'marca', 'modelo',
        'numero_serie', 'precio', 'fecha_adquisicion',
        'imagen', 'ubicacion', 'observacion', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['fecha_adquisicion' => 'date'];

    // ─── RELACIONES BASE ─────────────────────────────
    public function categoria()
    {
        return $this->belongsTo(CategoriaInventario::class, 'id_categoria');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }

    // ─── SPECS ───────────────────────────────────────
    public function specsComputacional()
    {
        return $this->hasOne(SpecsComputacional::class, 'id_item');
    }

    public function specsMobiliario()
    {
        return $this->hasOne(SpecsMobiliario::class, 'id_item');
    }

    public function specsVehiculo()
    {
        return $this->hasOne(SpecsVehiculo::class, 'id_item');
    }

    public function specsHerramienta()
    {
        return $this->hasOne(SpecsHerramienta::class, 'id_item');
    }

    public function specsInsumo()
    {
        return $this->hasOne(SpecsInsumo::class, 'id_item');
    }

    // ─── ASIGNACIONES ────────────────────────────────
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_item');
    }

    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class, 'id_item')
                    ->where('estado', 'Activo')
                    ->latest();
    }

    // ─── PROGRAMAS ───────────────────────────────────
    public function programas()
    {
        return $this->belongsToMany(Programa::class, 'item_programas', 'id_item', 'id_programa')
                    ->withPivot('fecha_instalacion')
                    ->withTimestamps();
    }

    public function formatos()
    {
        return $this->hasMany(Formato::class, 'id_item');
    }

    // ─── MERMAS ──────────────────────────────────────
    public function mermas()
    {
        return $this->hasMany(Merma::class, 'id_item');
    }

    // ─── HELPERS ─────────────────────────────────────
    public function getSpecs()
    {
        return match($this->categoria->nombre) {
            'Computacional' => $this->specsComputacional,
            'Mobiliario'    => $this->specsMobiliario,
            'Vehículo'      => $this->specsVehiculo,
            'Herramienta'   => $this->specsHerramienta,
            'Insumo'        => $this->specsInsumo,
            default         => null,
        };
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'Disponible'    => 'pill-green',
            'Asignado'      => 'pill-blue',
            'En mantención' => 'pill-amber',
            'De baja'       => 'pill-red',
            'Extraviado'    => 'pill-red',
            default         => 'pill-gray',
        };
    }

    // Categorías adicionales (muchos a muchos)
public function categorias()
{
    return $this->belongsToMany(
        CategoriaInventario::class,
        'item_categorias',
        'id_item',
        'id_categoria'
    )->withPivot('es_principal')->withTimestamps();
}

public function creadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}

public function editadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'updated_by');
}

public function eliminadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'deleted_by');
}

// Categoría principal (la original)

}