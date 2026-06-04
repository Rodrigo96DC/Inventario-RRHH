<?php

namespace App\Http\Controllers;

use App\Models\ItemInventario;
use App\Models\CategoriaInventario;
use App\Services\InventarioService;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function __construct(protected InventarioService $service) {}

 public function index(Request $request)
{
    $items = $this->service->listar($request);
    

    if ($request->ajax()) {
        return response()->json([
            'html'       => view('inventario.partials.tabla', compact('items'))->render(),
            'pagination' => view('inventario.partials.paginacion', compact('items'))->render(),
        ]);
    }

    return view('inventario.index', compact('items'));
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_categoria'     => 'required|exists:categorias_inventario,id',
            'nombre'           => 'required|string|max:255',
            'codigo_interno'   => 'nullable|string|unique:items_inventario,codigo_interno',
            'marca'            => 'nullable|string|max:255',
            'modelo'           => 'nullable|string|max:255',
            'numero_serie'     => 'nullable|string|max:255',
            'precio'           => 'nullable|numeric',
            'fecha_adquisicion'=> 'nullable|date',
            'ubicacion'        => 'nullable|string|max:255',
            'observacion'      => 'nullable|string',
            'estado'           => 'required|in:Disponible,Asignado,En mantención,De baja,Extraviado',
            'id_proveedor'     => 'nullable|exists:proveedores,id',
            'id_factura'       => 'nullable|exists:facturas,id',
            'imagen'           => 'nullable|image|max:2048',
            // Specs computacional
            'procesador'       => 'nullable|string',
            'ram'              => 'nullable|string',
            'tipo_disco'       => 'nullable|string',
            'capacidad_disco'  => 'nullable|string',
            'mac'              => 'nullable|string',
            'ip'               => 'nullable|string',
            'hostname'         => 'nullable|string',
            'numero_inventario'=> 'nullable|string',
            'entradas'         => 'nullable|string',
            // Specs mobiliario
            'material'         => 'nullable|string',
            'color'            => 'nullable|string',
            'dimensiones'      => 'nullable|string',
            // Specs vehículo
            'patente'          => 'nullable|string',
            'anio'             => 'nullable|integer',
            'tipo_combustible' => 'nullable|string',
            'numero_motor'     => 'nullable|string',
            'numero_chasis'    => 'nullable|string',
            'kilometraje'      => 'nullable|integer',
            // Specs herramienta
            'tipo'             => 'nullable|string',
            'medida'           => 'nullable|string',
            // Specs insumo
            'stock_inicial'    => 'nullable|integer',
            'stock_actual'     => 'nullable|integer',
            'unidad_medida'    => 'nullable|string',
            'stock_minimo'     => 'nullable|integer',
            'categorias_adicionales' => 'nullable|array',
        'categorias_adicionales.*' => 'exists:categorias_inventario,id',
        ]);

        $item = $this->service->crear($data, $request->file('imagen'));
            if ($request->categorias_adicionales) {
        $item->categorias()->sync(
            collect($request->categorias_adicionales)
                ->mapWithKeys(fn($id) => [$id => ['es_principal' => false]])
                ->toArray()
        );
    }

        return response()->json([
            'ok'      => true,
            'message' => 'Ítem registrado correctamente.',
            'data'    => $item->load('categoria'),
        ]);
    }

public function show(ItemInventario $inventario)
{
    $inventario->load([
        'categoria', 'categorias', 'proveedor', 'factura',
        'specsComputacional', 'specsMobiliario',
        'specsVehiculo', 'specsHerramienta', 'specsInsumo',
        'asignaciones.funcionario',
        'asignacionActiva.funcionario',
        'mermas', 'programas',
    ]);

    return response()->json([
        'ok'   => true,
        'data' => array_merge($inventario->getAttributes(), [
            'categoria'           => $inventario->categoria,
            'categorias'          => $inventario->categorias,
            'proveedor'           => $inventario->proveedor,
            'factura'             => $inventario->factura,
            'specs_computacional' => $inventario->specsComputacional,
            'specs_mobiliario'    => $inventario->specsMobiliario,
            'specs_vehiculo'      => $inventario->specsVehiculo,
            'specs_herramienta'   => $inventario->specsHerramienta,
            'specs_insumo'        => $inventario->specsInsumo,
            'asignaciones'        => $inventario->asignaciones,
            'asignacion_activa'   => $inventario->asignacionActiva,
            'mermas'              => $inventario->mermas,
            'programas'           => $inventario->programas,
        ]),
    ]);
}

public function perfil(ItemInventario $item)
{
    $item->load([
        'categoria', 'categorias', 'proveedor', 'factura',
        'specsComputacional', 'specsMobiliario',
        'specsVehiculo', 'specsHerramienta', 'specsInsumo',
        'asignaciones.funcionario',
        'asignacionActiva.funcionario',
        'mermas', 'programas',
    ]);

    $actividad  = $item->activities()->latest()->take(20)->get();
    $creadoPor  = \App\Models\User::find($item->created_by);
    $editadoPor = \App\Models\User::find($item->updated_by);

    return view('inventario.perfil', compact('item', 'actividad', 'creadoPor', 'editadoPor'));
}
    public function update(Request $request, ItemInventario $item)
    {
        $data = $request->validate([
            'id_categoria'     => 'required|exists:categorias_inventario,id',
            'nombre'           => 'required|string|max:255',
            'codigo_interno'   => "nullable|string|unique:items_inventario,codigo_interno,{$item->id}",
            'marca'            => 'nullable|string|max:255',
            'modelo'           => 'nullable|string|max:255',
            'numero_serie'     => 'nullable|string|max:255',
            'precio'           => 'nullable|numeric',
            'fecha_adquisicion'=> 'nullable|date',
            'ubicacion'        => 'nullable|string|max:255',
            'observacion'      => 'nullable|string',
            'estado'           => 'required|in:Disponible,Asignado,En mantención,De baja,Extraviado',
            'id_proveedor'     => 'nullable|exists:proveedores,id',
            'id_factura'       => 'nullable|exists:facturas,id',
            'imagen'           => 'nullable|image|max:2048',
            'procesador'       => 'nullable|string',
            'ram'              => 'nullable|string',
            'tipo_disco'       => 'nullable|string',
            'capacidad_disco'  => 'nullable|string',
            'mac'              => 'nullable|string',
            'ip'               => 'nullable|string',
            'hostname'         => 'nullable|string',
            'numero_inventario'=> 'nullable|string',
            'entradas'         => 'nullable|string',
            'material'         => 'nullable|string',
            'color'            => 'nullable|string',
            'dimensiones'      => 'nullable|string',
            'patente'          => 'nullable|string',
            'anio'             => 'nullable|integer',
            'tipo_combustible' => 'nullable|string',
            'numero_motor'     => 'nullable|string',
            'numero_chasis'    => 'nullable|string',
            'kilometraje'      => 'nullable|integer',
            'tipo'             => 'nullable|string',
            'medida'           => 'nullable|string',
            'stock_inicial'    => 'nullable|integer',
            'stock_actual'     => 'nullable|integer',
            'unidad_medida'    => 'nullable|string',
            'stock_minimo'     => 'nullable|integer',
              'categorias_adicionales'   => 'nullable|array',
        'categorias_adicionales.*' => 'exists:categorias_inventario,id',
        ]);

        $item = $this->service->actualizar($item, $data, $request->file('imagen'));

            // Actualizar categorías adicionales
    if ($request->has('categorias_adicionales')) {
        $item->categorias()->sync(
            collect($request->categorias_adicionales ?? [])
                ->mapWithKeys(fn($id) => [$id => ['es_principal' => false]])
                ->toArray()
        );
    }

        return response()->json([
            'ok'      => true,
            'message' => 'Ítem actualizado correctamente.',
            'data'    => $item,
        ]);
    }

    public function destroy(ItemInventario $item)
    {
        $this->service->eliminar($item);

        return response()->json([
            'ok'      => true,
            'message' => 'Ítem eliminado correctamente.',
        ]);
    }

    public function cambiarEstado(Request $request, ItemInventario $item)
    {
        $data = $request->validate([
            'estado' => 'required|in:Disponible,Asignado,En mantención,De baja,Extraviado',
        ]);

        $item->update($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Estado actualizado correctamente.',
        ]);
    }

    public function agregarPrograma(Request $request, ItemInventario $item)
{
    $data = $request->validate([
        'id_programa'      => 'required|exists:programas,id',
        'fecha_instalacion'=> 'nullable|date',
    ]);

    // Evitar duplicados
    if ($item->programas()->where('id_programa', $data['id_programa'])->exists()) {
        return response()->json(['ok' => false, 'message' => 'El programa ya está instalado.'], 422);
    }

    $item->programas()->attach($data['id_programa'], [
        'fecha_instalacion' => $data['fecha_instalacion'],
        'created_by'        => auth()->id(),
    ]);

    return response()->json(['ok' => true, 'message' => 'Programa agregado correctamente.']);
}

public function quitarPrograma(ItemInventario $item, $programaId)
{
    $item->programas()->detach($programaId);

    return response()->json(['ok' => true, 'message' => 'Programa removido correctamente.']);
}

public function registrarFormateo(Request $request, ItemInventario $item)
{
    $data = $request->validate([
        'fecha_formato' => 'required|date',
        'observacion'   => 'nullable|string',
    ]);

    // Guardar lista de programas actuales antes de limpiar
    $programasAnteriores = $item->programas->pluck('nombre')->toArray();

    // Eliminar todos los programas actuales
    $item->programas()->detach();

    // Registrar el formateo
    \App\Models\Formato::create([
        'id_item'              => $item->id,
        'fecha_formato'        => $data['fecha_formato'],
        'programas_anteriores' => $programasAnteriores,
        'observacion'          => $data['observacion'] ?? null,
        'created_by'           => auth()->id(),
    ]);

    return response()->json(['ok' => true, 'message' => 'Formateo registrado correctamente.']);
}
}