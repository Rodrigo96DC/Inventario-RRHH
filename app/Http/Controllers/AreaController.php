<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Services\AreaService;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct(protected AreaService $service) {}

    public function index(Request $request)
    {
        $areas = $this->service->listar($request);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('areas.partials.tabla', compact('areas'))->render(),
                'pagination' => view('areas.partials.paginacion', compact('areas'))->render(),
            ]);
        }

        return view('areas.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:areas,nombre',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $area = $this->service->crear($data);

        return response()->json(['ok' => true, 'message' => 'Área creada correctamente.', 'data' => $area]);
    }

    public function show(Area $area)
    {
        return response()->json(['ok' => true, 'data' => $area]);
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'nombre' => "required|string|max:255|unique:areas,nombre,{$area->id}",
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $area = $this->service->actualizar($area, $data);

        return response()->json(['ok' => true, 'message' => 'Área actualizada correctamente.', 'data' => $area]);
    }

    public function destroy(Area $area)
    {
        if (!$this->service->eliminar($area)) {
            return response()->json(['ok' => false, 'message' => 'No se puede eliminar, hay funcionarios asignados.'], 422);
        }

        return response()->json(['ok' => true, 'message' => 'Área eliminada correctamente.']);
    }

    public function toggleEstado(Area $area)
{
    $area    = $this->service->toggleEstado($area);
    $mensaje = $area->estado === 'Activo' ? 'Área habilitada.' : 'Área deshabilitada.';

    return response()->json(['ok' => true, 'message' => $mensaje]);
}
}