<?php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorService
{
    public function listar(Request $request)
    {
        return Proveedor::when($request->buscar, fn($q) =>
            $q->where('razon_social', 'like', "%{$request->buscar}%")
              ->orWhere('rut', 'like', "%{$request->buscar}%")
        )
        ->when($request->estado, fn($q) =>
            $q->where('estado', $request->estado)
        )
        ->latest()->paginate(15)->withQueryString();
    }

    public function crear(array $data): Proveedor
    {
        return Proveedor::create($data);
    }

    public function actualizar(Proveedor $proveedor, array $data): Proveedor
    {
        $proveedor->update($data);
        return $proveedor->fresh();
    }

    public function toggleEstado(Proveedor $proveedor): Proveedor
    {
        $proveedor->update([
            'estado' => $proveedor->estado === 'Activo' ? 'Inactivo' : 'Activo'
        ]);
        return $proveedor->fresh();
    }

    public function eliminar(Proveedor $proveedor): bool
    {
        if ($proveedor->facturas()->count() > 0) return false;
        $proveedor->delete();
        return true;
    }

      public function promedioProveedor(int $proveedorId): float
    {
        return \App\Models\EvaluacionFactura::whereHas('factura', fn($q) =>
            $q->where('id_proveedor', $proveedorId)
        )->avg('calificacion') ?? 0;
    }
}