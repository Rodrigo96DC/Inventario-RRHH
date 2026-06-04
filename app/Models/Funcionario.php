<?php

namespace App\Models;

use App\Traits\TieneTrazabilidad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use SoftDeletes, TieneTrazabilidad;

    protected $table = 'funcionarios';

    protected $fillable = [
        'user_id', 'id_region', 'id_provincia', 'id_comuna',
        'id_cargo', 'id_area', 'rut', 'nombre', 'apellido',
        'email', 'telefono', 'direccion', 'avatar',
        'tipo_contrato', 'fecha_ingreso', 'estado', 'fecha_fin_contrato',
        'dias_aviso_contrato',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_fin_contrato' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class, 'id_comuna');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminadoPor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function getInicialesAttribute(): string
    {
        return strtoupper(substr($this->nombre, 0, 1).substr($this->apellido, 0, 1));
    }
}
