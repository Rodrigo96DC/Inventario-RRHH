<?php

namespace App\Services;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturaService
{
    public function listar(Request $request, int $proveedorId)
    {
        return Factura::where('id_proveedor', $proveedorId)
            ->when($request->buscar, fn($q) =>
                $q->where('numero_factura', 'like', "%{$request->buscar}%")
            )
            ->with('evaluacion')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function crear(array $data, $archivo = null): Factura
    {
        if ($archivo) {
            $data['archivo'] = $archivo->store('facturas', 'public');
        }
        return Factura::create($data);
    }

    public function actualizar(Factura $factura, array $data, $archivo = null): Factura
    {
        if ($archivo) {
            if ($factura->archivo) Storage::disk('public')->delete($factura->archivo);
            $data['archivo'] = $archivo->store('facturas', 'public');
        }
        $factura->update($data);
        return $factura->fresh();
    }

    public function eliminar(Factura $factura): bool
    {
        if ($factura->items()->count() > 0) return false;
        if ($factura->archivo) Storage::disk('public')->delete($factura->archivo);
        $factura->delete();
        return true;
    }

  
}