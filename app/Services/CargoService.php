<?php

namespace App\Services;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoService
{
    public function listar(Request $request)
    {
        return Cargo::when($request->buscar, fn($q) =>
            $q->where('nombre', 'like', "%{$request->buscar}%")
        )->latest()->paginate(10)->withQueryString();
    }

    public function crear(array $data): Cargo
    {
        return Cargo::create($data);
    }

    public function actualizar(Cargo $cargo, array $data): Cargo
    {
        $cargo->update($data);
        return $cargo->fresh();
    }

    public function eliminar(Cargo $cargo): bool
    {
        if ($cargo->funcionarios()->count() > 0) return false;
        $cargo->delete();
        return true;
    }

    public function toggleEstado(Cargo $cargo): Cargo
{
    $cargo->update([
        'estado' => $cargo->estado === 'Activo' ? 'Inactivo' : 'Activo'
    ]);
    return $cargo->fresh();
}
}