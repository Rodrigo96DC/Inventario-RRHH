<?php

namespace App\Services;

use App\Models\CategoriaInventario;
use Illuminate\Http\Request;

class CategoriaInventarioService
{
    public function listar(Request $request)
    {
        return CategoriaInventario::when($request->buscar, fn($q) =>
            $q->where('nombre', 'like', "%{$request->buscar}%")
        )->latest()->paginate(10)->withQueryString();
    }

    public function crear(array $data): CategoriaInventario
    {
        return CategoriaInventario::create($data);
    }

    public function actualizar(CategoriaInventario $categoria, array $data): CategoriaInventario
    {
        $categoria->update($data);
        return $categoria->fresh();
    }

    public function toggleEstado(CategoriaInventario $categoria): CategoriaInventario
    {
        $categoria->update([
            'estado' => $categoria->estado === 'Activo' ? 'Inactivo' : 'Activo'
        ]);
        return $categoria->fresh();
    }
}