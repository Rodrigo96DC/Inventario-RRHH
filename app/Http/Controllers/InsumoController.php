<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Factura;
use App\Services\InsumoService;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function __construct(protected InsumoService $service) {}

    public function index(Request $request)
    {
        $insumos = $this->service->listar($request);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('insumos.partials.tabla', compact('insumos'))->render(),
                'pagination' => view('insumos.partials.paginacion', compact('insumos'))->render(),
            ]);
        }

        return view('insumos.index', compact('insumos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'unidad_medida'    => 'required|string|max:50',
            'stock_minimo'     => 'required|integer|min:0',
            'metodo_valuacion' => 'required|in:FIFO,LIFO',
            'id_categoria'     => 'nullable|exists:categorias_inventario,id',
            'estado'           => 'required|in:Activo,Inactivo',
        ]);

        $insumo = $this->service->crear($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Insumo creado correctamente.',
            'data'    => $insumo,
        ]);
    }

public function show(Request $request, Insumo $insumo)
{
    // Si tiene header Ajax o no es una petición de navegador normal
    if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With')) {
        return response()->json([
            'ok'   => true,
            'data' => $insumo->load('categoria'),
        ]);
    }

    $insumo->load(['categoria', 'lotes.factura', 'movimientos.funcionario', 'movimientos.lote', 'creadoPor']);
    $actividad = $insumo->activities()->latest()->take(20)->get();

    return view('insumos.perfil', compact('insumo', 'actividad'));
}

    public function update(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'unidad_medida'    => 'required|string|max:50',
            'stock_minimo'     => 'required|integer|min:0',
            'metodo_valuacion' => 'required|in:FIFO,LIFO',
            'id_categoria'     => 'nullable|exists:categorias_inventario,id',
            'estado'           => 'required|in:Activo,Inactivo',
        ]);

        $insumo = $this->service->actualizar($insumo, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Insumo actualizado correctamente.',
            'data'    => $insumo,
        ]);
    }

    public function toggleEstado(Insumo $insumo)
    {
        $insumo = $this->service->toggleEstado($insumo);
        $mensaje = $insumo->estado === 'Activo' ? 'Insumo habilitado.' : 'Insumo deshabilitado.';

        return response()->json(['ok' => true, 'message' => $mensaje]);
    }

    public function destroy(Insumo $insumo)
    {
        if ($insumo->movimientos()->count() > 0) {
            return response()->json([
                'ok'      => false,
                'message' => 'No se puede eliminar, tiene movimientos registrados.',
            ], 422);
        }

        $insumo->delete();

        return response()->json([
            'ok'      => true,
            'message' => 'Insumo eliminado correctamente.',
        ]);
    }

    // ─── ENTRADA MASIVA ──────────────────────────────────────────
    public function entrada(Request $request, Insumo $insumo)
    {
        $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.cantidad'         => 'required|integer|min:1',
            'items.*.fecha'            => 'required|date',
            'items.*.precio_unitario'  => 'nullable|numeric',
            'items.*.fecha_vencimiento'=> 'nullable|date',
            'items.*.observacion'      => 'nullable|string',
            'id_factura'               => 'nullable|exists:facturas,id',
        ]);

        $this->service->entrada($insumo, $request->items, $request->id_factura);

        return response()->json([
            'ok'      => true,
            'message' => 'Entrada registrada correctamente.',
        ]);
    }

    // ─── SALIDA (despacho) ───────────────────────────────────────
    public function salida(Request $request, Insumo $insumo)
    {
        $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'cantidad'       => 'required|integer|min:1',
            'fecha'          => 'required|date',
            'observacion'    => 'nullable|string',
        ]);

        $ok = $this->service->salida(
            $insumo,
            $request->cantidad,
            $request->id_funcionario,
            $request->fecha,
            $request->observacion
        );

        if (!$ok) {
            return response()->json([
                'ok'      => false,
                'message' => 'Stock insuficiente para despachar.',
            ], 422);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Despacho registrado correctamente.',
        ]);
    }

    // ─── BUSCAR INSUMOS (para perfil funcionario) ────────────────
    public function buscar(Request $request)
    {
        $insumos = Insumo::where('estado', 'Activo')
            ->where('stock_actual', '>', 0)
            ->where(function($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%");
            })
            ->with('categoria')
            ->take(20)
            ->get();

        return response()->json($insumos);
    }

    public function movimientosFuncionario(\App\Models\Funcionario $funcionario)
{
    $movimientos = \App\Models\InsumoMovimiento::with('insumo')
        ->where('id_funcionario', $funcionario->id)
        ->where('tipo', 'Salida')
        ->latest()
        ->get();

    return response()->json($movimientos);
}
}