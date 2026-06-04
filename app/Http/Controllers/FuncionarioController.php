<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Services\FuncionarioService;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function __construct(protected FuncionarioService $service) {}

    public function index(Request $request)
    {
        $funcionarios = $this->service->listar($request);
        $areas        = \App\Models\Area::where('estado', 'Activo')->get();

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('funcionarios.partials.tabla', compact('funcionarios'))->render(),
                'pagination' => view('funcionarios.partials.paginacion', compact('funcionarios'))->render(),
            ]);
        }

        return view('funcionarios.index', compact('funcionarios', 'areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rut'           => 'required|string|unique:funcionarios,rut',
            'nombre'        => 'required|string|max:255',
            'apellido'      => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'telefono'      => 'nullable|string|max:20',
            'direccion'     => 'nullable|string|max:255',
            'id_region'     => 'nullable|exists:regiones,id',
            'id_provincia'  => 'nullable|exists:provincias,id',
            'id_comuna'     => 'nullable|exists:comunas,id',
            'id_cargo'      => 'nullable|exists:cargos,id',
            'id_area'       => 'nullable|exists:areas,id',
            'tipo_contrato' => 'nullable|in:Planta,Contrata,Honorarios',
            'fecha_ingreso' => 'nullable|date',
            'estado'        => 'required|in:Activo,Inactivo,Licencia,Vacaciones',
            'fecha_fin_contrato'  => 'nullable|date',
'dias_aviso_contrato' => 'nullable|integer|in:15,30,60,90',
        ]);

        $funcionario = $this->service->crear($data);

        return response()->json([
            'ok'      => true,
            'message' => 'Funcionario creado correctamente.',
            'data'    => $funcionario->load(['region', 'cargo', 'area']),
        ]);
    }

    public function show(Funcionario $funcionario)
    {
        return response()->json([
            'ok'   => true,
            'data' => $funcionario->load(['region', 'provincia', 'comuna', 'cargo', 'area']),
        ]);
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $data = $request->validate([
            'rut'           => "required|string|unique:funcionarios,rut,{$funcionario->id}",
            'nombre'        => 'required|string|max:255',
            'apellido'      => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'telefono'      => 'nullable|string|max:20',
            'direccion'     => 'nullable|string|max:255',
            'id_region'     => 'nullable|exists:regiones,id',
            'id_provincia'  => 'nullable|exists:provincias,id',
            'id_comuna'     => 'nullable|exists:comunas,id',
            'id_cargo'      => 'nullable|exists:cargos,id',
            'id_area'       => 'nullable|exists:areas,id',
            'tipo_contrato' => 'nullable|in:Planta,Contrata,Honorarios',
            'fecha_ingreso' => 'nullable|date',
            'estado'        => 'required|in:Activo,Inactivo,Licencia,Vacaciones',
            'fecha_fin_contrato'  => 'nullable|date',
'dias_aviso_contrato' => 'nullable|integer|in:15,30,60,90',
        ]);

        $funcionario = $this->service->actualizar($funcionario, $data);
  \Log::info('Datos recibidos:', $request->all());
        return response()->json([
            'ok'      => true,
            'message' => 'Funcionario actualizado correctamente.',
            'data'    => $funcionario,
        ]);
    }

    public function destroy(Funcionario $funcionario)
    {
        $this->service->eliminar($funcionario);

        return response()->json([
            'ok'      => true,
            'message' => 'Funcionario eliminado correctamente.',
        ]);
    }

    public function provincias(Request $request)
    {
        return response()->json(
            $this->service->provincias((int) $request->id_region)
        );
    }

    public function comunas(Request $request)
    {
        return response()->json(
            $this->service->comunas((int) $request->id_provincia)
        );
    }

    public function cambiarEstado(Request $request, Funcionario $funcionario)
{
    $data = $request->validate([
        'estado' => 'required|in:Activo,Inactivo',
    ]);

    $funcionario = $this->service->actualizar($funcionario, $data);

    $mensaje = $data['estado'] === 'Activo'
        ? 'Funcionario habilitado correctamente.'
        : 'Funcionario deshabilitado correctamente.';

    return response()->json(['ok' => true, 'message' => $mensaje]);
}

public function perfil(Funcionario $funcionario)
{
    $funcionario->load(['region', 'provincia', 'comuna', 'cargo', 'area', 'creadoPor', 'editadoPor']);
    $actividad = $funcionario->activities()->latest()->take(20)->get();

    return view('funcionarios.perfil', compact('funcionario', 'actividad'));
}
}