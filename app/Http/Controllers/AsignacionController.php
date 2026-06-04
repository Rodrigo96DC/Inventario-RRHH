<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\ItemInventario;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_item'         => 'required|exists:items_inventario,id',
            'id_funcionario'  => 'required|exists:funcionarios,id',
            'cantidad'        => 'required|integer|min:1',
            'fecha_asignacion'=> 'required|date',
            'observacion'     => 'nullable|string',
            'estado'          => 'required|in:Activo,Devuelto',
        ]);

        // Actualizar estado del item a Asignado
        ItemInventario::find($data['id_item'])->update(['estado' => 'Asignado']);

        $asignacion = Asignacion::create($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Asignación registrada correctamente.',
            'data'    => $asignacion,
        ]);
    }

    public function devolver(Asignacion $asignacion)
    {
        $asignacion->update([
            'estado'           => 'Devuelto',
            'fecha_devolucion' => now()->toDateString(),
        ]);

        // Si no hay más asignaciones activas cambiar estado a Disponible
        $tieneActivas = Asignacion::where('id_item', $asignacion->id_item)
            ->where('estado', 'Activo')
            ->exists();

        if (!$tieneActivas) {
            $asignacion->item->update(['estado' => 'Disponible']);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Devolución registrada correctamente.',
        ]);
    }


public function storeMasiva(Request $request)
{
    $request->validate([
        'id_funcionario' => 'required|exists:funcionarios,id',
        'items'          => 'required|array|min:1',
        'items.*.id'     => 'required|exists:items_inventario,id',
        'items.*.cantidad' => 'required|integer|min:1',
        'fecha'          => 'required|date',
        'observacion'    => 'nullable|string',
    ]);

    $creados = 0;
    foreach ($request->items as $itemData) {
        $item = \App\Models\ItemInventario::find($itemData['id']);
        if (!$item || $item->estado !== 'Disponible') continue;

        \App\Models\Asignacion::create([
            'id_item'          => $itemData['id'],
            'id_funcionario'   => $request->id_funcionario,
            'fecha_asignacion' => $request->fecha,
            'observacion'      => $request->observacion,
            'estado'           => 'Activo',
            'cantidad'         => $itemData['cantidad'],
        ]);

        $item->update(['estado' => 'Asignado']);
        $creados++;
    }

    return response()->json([
        'ok'      => true,
        'message' => "{$creados} ítem(s) asignados correctamente.",
    ]);
}

public function buscarItems(Request $request)
{
    $items = \App\Models\ItemInventario::with('categoria')
        ->where('estado', 'Disponible')
        ->where(function($q) use ($request) {
            $q->where('nombre', 'like', "%{$request->buscar}%")
              ->orWhere('codigo_interno', 'like', "%{$request->buscar}%")
              ->orWhere('numero_serie', 'like', "%{$request->buscar}%");
        })
        ->take(20)
        ->get();

    return response()->json($items);
}

public function itemsFuncionario(\App\Models\Funcionario $funcionario)
{
    $asignaciones = \App\Models\Asignacion::with('item.categoria')
        ->where('id_funcionario', $funcionario->id)
        ->where('estado', 'Activo')
        ->get();

    return response()->json([
        'funcionario' => [
            'id'       => $funcionario->id,
            'nombre'   => $funcionario->nombre_completo,
            'cargo'    => $funcionario->cargo->nombre ?? '—',
            'iniciales'=> $funcionario->iniciales,
        ],
        'asignaciones' => $asignaciones,
    ]);
}
}