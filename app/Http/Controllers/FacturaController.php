<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Proveedor;
use App\Models\EvaluacionFactura;
use App\Services\FacturaService;
use App\Services\ProveedorService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
class FacturaController extends Controller
{
    public function __construct(
        protected FacturaService $facturaService,
        protected ProveedorService $proveedorService
    ) {}

public function index(Request $request, Proveedor $proveedor)
{
    $facturas  = $this->facturaService->listar($request, $proveedor->id);
    $promedio  = $this->proveedorService->promedioProveedor($proveedor->id);
    $actividad = Activity::where(function($q) use ($proveedor) {
        $q->where(function($q2) use ($proveedor) {
            $q2->where('subject_type', Proveedor::class)
               ->where('subject_id', $proveedor->id);
        })->orWhere(function($q2) use ($proveedor) {
            $q2->where('subject_type', Factura::class)
               ->whereIn('subject_id', $proveedor->facturas->pluck('id'));
        });
    })
    ->latest()
    ->take(30)
    ->get();

    $proveedor->load(['facturas.evaluacion.creadoPor']);

    if ($request->ajax()) {
        return response()->json([
            'html'       => view('proveedores.partials.facturas', compact('facturas', 'proveedor'))->render(),
            'pagination' => view('proveedores.partials.facturas_paginacion', compact('facturas', 'proveedor'))->render(),
        ]);
    }

    return view('proveedores.perfil', compact('proveedor', 'facturas', 'promedio', 'actividad'));
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_proveedor'   => 'required|exists:proveedores,id',
            'numero_factura' => 'required|string|unique:facturas,numero_factura',
            'fecha_factura'  => 'required|date',
            'monto_total'    => 'nullable|numeric',
            'archivo'        => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'observacion'    => 'nullable|string',
        ]);

        $factura = $this->facturaService->crear($data, $request->file('archivo'));

        return response()->json([
            'ok'      => true,
            'message' => 'Factura registrada correctamente.',
            'data'    => $factura,
        ]);
    }

    public function update(Request $request, Factura $factura)
    {
        $data = $request->validate([
            'numero_factura' => "required|string|unique:facturas,numero_factura,{$factura->id}",
            'fecha_factura'  => 'required|date',
            'monto_total'    => 'nullable|numeric',
            'archivo'        => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'observacion'    => 'nullable|string',
        ]);

        $factura = $this->facturaService->actualizar($factura, $data, $request->file('archivo'));

        return response()->json([
            'ok'      => true,
            'message' => 'Factura actualizada correctamente.',
            'data'    => $factura,
        ]);
    }

    public function destroy(Factura $factura)
    {
        if (!$this->facturaService->eliminar($factura)) {
            return response()->json([
                'ok'      => false,
                'message' => 'No se puede eliminar, tiene ítems asociados.',
            ], 422);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Factura eliminada correctamente.',
        ]);
    }

    public function evaluar(Request $request, Factura $factura)
    {
        $data = $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'nullable|string',
        ]);

        EvaluacionFactura::updateOrCreate(
            ['id_factura' => $factura->id],
            [
                'calificacion' => $data['calificacion'],
                'comentario'   => $data['comentario'] ?? null,
                'updated_by'   => auth()->id(),
            ]
        );

        return response()->json([
            'ok'      => true,
            'message' => 'Evaluación guardada correctamente.',
        ]);
    }

    public function buscar(Request $request)
{
    $facturas = Factura::with('proveedor')
        ->where('numero_factura', 'like', "%{$request->buscar}%")
        ->take(10)
        ->get();

    return response()->json($facturas);
}
}