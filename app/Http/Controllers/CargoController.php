<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Services\CargoService;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function __construct(protected CargoService $service) {}

    public function index(Request $request)
    {
        $cargos = $this->service->listar($request);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('cargos.partials.tabla', compact('cargos'))->render(),
                'pagination' => view('cargos.partials.paginacion', compact('cargos'))->render(),
            ]);
        }

        return view('cargos.index', compact('cargos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre',
            'grado'  => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $cargo = $this->service->crear($data);

        return response()->json(['ok' => true, 'message' => 'Cargo creado correctamente.', 'data' => $cargo]);
    }

    public function show(Cargo $cargo)
    {
        return response()->json(['ok' => true, 'data' => $cargo]);
    }

    public function update(Request $request, Cargo $cargo)
    {
        $data = $request->validate([
            'nombre' => "required|string|max:255|unique:cargos,nombre,{$cargo->id}",
            'grado'  => 'nullable|string|max:50',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $cargo = $this->service->actualizar($cargo, $data);

        return response()->json(['ok' => true, 'message' => 'Cargo actualizado correctamente.', 'data' => $cargo]);
    }

    public function destroy(Cargo $cargo)
    {
        if (!$this->service->eliminar($cargo)) {
            return response()->json(['ok' => false, 'message' => 'No se puede eliminar, hay funcionarios asignados.'], 422);
        }

        return response()->json(['ok' => true, 'message' => 'Cargo eliminado correctamente.']);
    }

    public function toggleEstado(Cargo $cargo)
{
    $cargo   = $this->service->toggleEstado($cargo);
    $mensaje = $cargo->estado === 'Activo' ? 'Cargo habilitado.' : 'Cargo deshabilitado.';

    return response()->json(['ok' => true, 'message' => $mensaje]);
}
}