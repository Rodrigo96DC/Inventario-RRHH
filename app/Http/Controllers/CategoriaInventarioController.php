<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInventario;
use App\Services\CategoriaInventarioService;
use Illuminate\Http\Request;

class CategoriaInventarioController extends Controller
{
    public function __construct(protected CategoriaInventarioService $service) {}

    public function index(Request $request)
    {
        $categorias = $this->service->listar($request);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('categorias.partials.tabla', compact('categorias'))->render(),
                'pagination' => view('categorias.partials.paginacion', compact('categorias'))->render(),
            ]);
        }

        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_inventario,nombre',
            'icono'  => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $categoria = $this->service->crear($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Categoría creada correctamente.',
            'data'    => $categoria,
        ]);
    }

    public function show(CategoriaInventario $categoria)
    {
        return response()->json(['ok' => true, 'data' => $categoria]);
    }

    public function update(Request $request, CategoriaInventario $categoria)
    {
        $data = $request->validate([
            'nombre' => "required|string|max:255|unique:categorias_inventario,nombre,{$categoria->id}",
            'icono'  => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $categoria = $this->service->actualizar($categoria, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Categoría actualizada correctamente.',
            'data'    => $categoria,
        ]);
    }

    public function toggleEstado(CategoriaInventario $categoria)
    {
        $categoria = $this->service->toggleEstado($categoria);
        $mensaje   = $categoria->estado === 'Activo' ? 'Categoría habilitada.' : 'Categoría deshabilitada.';

        return response()->json(['ok' => true, 'message' => $mensaje, 'data' => $categoria]);
    }

    public function destroy(CategoriaInventario $categoria)
{
    if ($categoria->items()->count() > 0) {
        return response()->json([
            'ok'      => false,
            'message' => 'No se puede eliminar, hay ítems asociados a esta categoría.',
        ], 422);
    }

    $categoria->delete();

    return response()->json([
        'ok'      => true,
        'message' => 'Categoría eliminada correctamente.',
    ]);
}
}