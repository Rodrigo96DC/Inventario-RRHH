<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\ProveedorService;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function __construct(protected ProveedorService $service) {}

    public function index(Request $request)
    {
        $proveedores = $this->service->listar($request);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('proveedores.partials.tabla', compact('proveedores'))->render(),
                'pagination' => view('proveedores.partials.paginacion', compact('proveedores'))->render(),
            ]);
        }

        return view('proveedores.index', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'rut'          => 'required|string|unique:proveedores,rut',
            'contacto'     => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'direccion'    => 'nullable|string|max:255',
            'estado'       => 'required|in:Activo,Inactivo',
        ]);

        $proveedor = $this->service->crear($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Proveedor creado correctamente.',
            'data'    => $proveedor,
        ]);
    }

public function show(Proveedor $proveedor)
{
    return response()->json([
        'ok'   => true,
        'data' => [
            'id'           => $proveedor->id,
            'razon_social' => $proveedor->razon_social,
            'rut'          => $proveedor->rut,
            'contacto'     => $proveedor->contacto,
            'telefono'     => $proveedor->telefono,
            'email'        => $proveedor->email,
            'direccion'    => $proveedor->direccion,
            'estado'       => $proveedor->estado,
            'facturas'     => $proveedor->facturas,
        ],
    ]);
}

    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'rut'          => "required|string|unique:proveedores,rut,{$proveedor->id}",
            'contacto'     => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'direccion'    => 'nullable|string|max:255',
            'estado'       => 'required|in:Activo,Inactivo',
        ]);

        $proveedor = $this->service->actualizar($proveedor, $data);

        return response()->json([
            'ok'      => true,
            'message' => 'Proveedor actualizado correctamente.',
            'data'    => $proveedor,
        ]);
    }

    public function toggleEstado(Proveedor $proveedor)
    {
        $proveedor = $this->service->toggleEstado($proveedor);
        $mensaje   = $proveedor->estado === 'Activo' ? 'Proveedor habilitado.' : 'Proveedor deshabilitado.';

        return response()->json(['ok' => true, 'message' => $mensaje]);
    }

    public function destroy(Proveedor $proveedor)
    {
        if (!$this->service->eliminar($proveedor)) {
            return response()->json([
                'ok'      => false,
                'message' => 'No se puede eliminar, tiene facturas asociadas.',
            ], 422);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Proveedor eliminado correctamente.',
        ]);
    }
}


