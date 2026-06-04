<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TieneTrazabilidad;

class Proveedor extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'proveedores';

    protected $fillable = [
        'razon_social', 'rut', 'contacto', 'telefono',
        'email', 'direccion', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_proveedor');
    }

    public function getPromedioCalificacionAttribute(): float
    {
        return EvaluacionFactura::whereHas('factura', fn($q) =>
            $q->where('id_proveedor', $this->id)
        )->avg('calificacion') ?? 0;
    }

    public function getEstrellaAttribute(): string
    {
        $promedio = $this->promedio_calificacion;
        $llenas   = floor($promedio);
        $media    = ($promedio - $llenas) >= 0.5 ? 1 : 0;
        $vacias   = 5 - $llenas - $media;

        $html  = str_repeat('<i class="ti ti-star-filled" style="color:#f59e0b;"></i>', $llenas);
        $html .= $media ? '<i class="ti ti-star-half-filled" style="color:#f59e0b;"></i>' : '';
        $html .= str_repeat('<i class="ti ti-star" style="color:#d1d5db;"></i>', $vacias);

        return $html;
    }
}