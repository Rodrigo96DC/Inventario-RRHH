<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TieneTrazabilidad;

class EvaluacionFactura extends Model
{
    use TieneTrazabilidad;

    protected $table = 'evaluaciones_factura';

    protected $fillable = [
        'id_factura', 'calificacion', 'comentario',
        'created_by', 'updated_by',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }
    public function creadoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}
}