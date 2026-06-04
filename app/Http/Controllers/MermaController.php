<?php

namespace App\Http\Controllers;

use App\Models\Merma;
use App\Models\ItemInventario;
use Illuminate\Http\Request;

class MermaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_item'       => 'required|exists:items_inventario,id',
            'cantidad'      => 'required|integer|min:1',
            'fecha_merma'   => 'required|date',
            'descripcion'   => 'required|string',
            'lugar_guardado'=> 'nullable|string',
        ]);

        // Descontar del stock actual si es insumo
        $item = ItemInventario::find($data['id_item']);
        if ($item->categoria->nombre === 'Insumo' && $item->specsInsumo) {
            $nuevoStock = max(0, $item->specsInsumo->stock_actual - $data['cantidad']);
            $item->specsInsumo->update(['stock_actual' => $nuevoStock]);
        }

        $merma = Merma::create($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Merma registrada correctamente.',
            'data'    => $merma,
        ]);
    }
}