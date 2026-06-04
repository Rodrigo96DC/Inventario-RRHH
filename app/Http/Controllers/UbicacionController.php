<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Puesto;
use App\Services\UbicacionService;
use Illuminate\Http\Request;
class UbicacionController extends Controller
{
    public function __construct(protected UbicacionService $service) {}

    // ─── VISTA PRINCIPAL ─────────────────────────────────────────
    public function index()
    {
        $edificios = $this->service->listarEdificios();
        return view('ubicaciones.index', compact('edificios'));
    }

    // ─── PLANO DEL PISO ──────────────────────────────────────────
    public function plano(Piso $piso)
    {
        $piso->load('edificio');
        $puestos = $this->service->puestosConFuncionario($piso);
        return view('ubicaciones.plano', compact('piso', 'puestos'));
    }

    // ─── EDIFICIOS ───────────────────────────────────────────────
    public function storeEdificio(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'estado'    => 'required|in:Activo,Inactivo',
        ]);

        $edificio = $this->service->crearEdificio($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Edificio creado correctamente.',
            'data'    => $edificio,
        ]);
    }

    public function updateEdificio(Request $request, Edificio $edificio)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'estado'    => 'required|in:Activo,Inactivo',
        ]);

        $edificio = $this->service->actualizarEdificio($edificio, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Edificio actualizado correctamente.',
            'data'    => $edificio,
        ]);
    }

    // ─── PISOS ───────────────────────────────────────────────────
    public function storePiso(Request $request)
    {
        $data = $request->validate([
            'id_edificio' => 'required|exists:edificios,id',
            'nombre'      => 'required|string|max:255',
            'numero'      => 'required|integer',
            'imagen'      => 'nullable|image|max:5120',
            'estado'      => 'required|in:Activo,Inactivo',
        ]);

        $piso = $this->service->crearPiso($data, $request->file('imagen'));

        return response()->json([
            'ok'      => true,
            'message' => 'Piso creado correctamente.',
            'data'    => $piso,
        ]);
    }

    public function updatePiso(Request $request, Piso $piso)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:255',
            'numero'  => 'required|integer',
            'imagen'  => 'nullable|image|max:5120',
            'estado'  => 'required|in:Activo,Inactivo',
        ]);

        $piso = $this->service->actualizarPiso($piso, $data, $request->file('imagen'));

        return response()->json([
            'ok'      => true,
            'message' => 'Piso actualizado correctamente.',
            'data'    => $piso,
        ]);
    }

    // ─── PUESTOS ─────────────────────────────────────────────────
    public function storePuesto(Request $request)
    {
        $data = $request->validate([
            'id_piso' => 'required|exists:pisos,id',
            'numero'  => 'required|string|max:50',
            'seccion' => 'nullable|string|max:50',
            'x1'      => 'required|numeric',
            'y1'      => 'required|numeric',
            'x2'      => 'required|numeric',
            'y2'      => 'required|numeric',
        ]);

        $puesto = $this->service->crearPuesto($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Puesto creado correctamente.',
            'data'    => $puesto,
        ]);
    }

    public function updatePuesto(Request $request, Puesto $puesto)
    {
        $data = $request->validate([
            'numero'  => 'required|string|max:50',
            'seccion' => 'nullable|string|max:50',
            'x1'      => 'required|numeric',
            'y1'      => 'required|numeric',
            'x2'      => 'required|numeric',
            'y2'      => 'required|numeric',
        ]);

        $puesto = $this->service->actualizarPuesto($puesto, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Puesto actualizado correctamente.',
            'data'    => $puesto,
        ]);
    }

    public function destroyPuesto(Puesto $puesto)
    {
        $this->service->eliminarPuesto($puesto);

        return response()->json([
            'ok'      => true,
            'message' => 'Puesto eliminado correctamente.',
        ]);
    }

    // ─── ASIGNACIONES ────────────────────────────────────────────
    public function asignar(Request $request, Puesto $puesto)
    {
        $data = $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'fecha_inicio'   => 'required|date',
            'observacion'    => 'nullable|string',
        ]);

        $this->service->asignarFuncionario($puesto, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Funcionario asignado correctamente.',
        ]);
    }

    public function liberar(Puesto $puesto)
    {
        $this->service->liberarPuesto($puesto);

        return response()->json([
            'ok'      => true,
            'message' => 'Puesto liberado correctamente.',
        ]);
    }

    // ─── PUESTOS JSON (para el plano) ────────────────────────────
    public function puestosJson(Piso $piso)
    {
        return response()->json($this->service->puestosConFuncionario($piso));
    }

    public function toggleEstadoPuesto(Request $request, Puesto $puesto)
{
    $data = $request->validate([
        'estado' => 'required|in:Disponible,Ocupado,Inhabilitado',
    ]);

    $puesto->update(['estado' => $data['estado']]);

    return response()->json([
        'ok'      => true,
        'message' => 'Estado del puesto actualizado.',
        'data'    => $puesto,
    ]);
}

public function limpiarPiso(Request $request, Piso $piso)
{
    $request->validate([
        'frase'  => 'required|string',
        'motivo' => 'nullable|string',
    ]);

    if ($request->frase !== 'LIMPIAR PISO') {
        return response()->json([
            'ok'      => false,
            'message' => 'La frase de confirmación no es correcta.',
        ], 422);
    }

    $historial = $this->service->limpiarPiso($piso, $request->motivo ?? '');

    return response()->json([
        'ok'      => true,
        'message' => 'Piso limpiado correctamente.',
        'data'    => $historial,
    ]);
}

public function rollbackPiso(int $historialId)
{
    $historial = \App\Models\HistorialPiso::findOrFail($historialId);

    if ($historial->revertido) {
        return response()->json([
            'ok'      => false,
            'message' => 'Este historial ya fue revertido.',
        ], 422);
    }

    $this->service->rollbackPiso($historial);

    return response()->json([
        'ok'      => true,
        'message' => 'Piso restaurado correctamente.',
    ]);
}

public function historialPiso(Piso $piso)
{
    $historial = $this->service->historialPiso($piso);

    return response()->json($historial);
}
}