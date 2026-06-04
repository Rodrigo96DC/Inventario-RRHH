<?php

namespace App\Services;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaService
{
    public function listar(Request $request)
    {
        return Area::when($request->buscar, fn($q) =>
            $q->where('nombre', 'like', "%{$request->buscar}%")
        )->latest()->paginate(10)->withQueryString();
    }

    public function crear(array $data): Area
    {
        return Area::create($data);
    }

    public function actualizar(Area $area, array $data): Area
    {
        $area->update($data);
        return $area->fresh();
    }

    public function eliminar(Area $area): bool
    {
        if ($area->funcionarios()->count() > 0) return false;
        $area->delete();
        return true;
    }

    public function toggleEstado(Area $area): Area
{
    $area->update([
        'estado' => $area->estado === 'Activo' ? 'Inactivo' : 'Activo'
    ]);
    return $area->fresh();
}
}