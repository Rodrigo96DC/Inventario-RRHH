<?php

namespace App\Services;

use App\Models\Funcionario;
use App\Models\Provincia;
use App\Models\Comuna;
use Illuminate\Http\Request;

class FuncionarioService
{
public function listar(Request $request)
{
    return Funcionario::with(['cargo', 'area'])
        ->when($request->buscar, function($q) use ($request) {
            $palabras = explode(' ', trim($request->buscar));
            foreach ($palabras as $palabra) {
                if (strlen($palabra) < 2) continue;
                $q->where(function($q2) use ($palabra) {
                    $q2->where('nombre',   'like', "%{$palabra}%")
                       ->orWhere('apellido', 'like', "%{$palabra}%")
                       ->orWhereRaw("REPLACE(rut, '.', '') LIKE ?", ["%".str_replace('.', '', $palabra)."%"]);
                });
            }
        })
        ->when($request->estado, fn($q) =>
            $q->where('estado', $request->estado)
        )
        ->when($request->id_area, fn($q) =>
            $q->where('id_area', $request->id_area)
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();
}

    public function crear(array $data): Funcionario
    {
        return Funcionario::create($this->limpiarData($data));
    }

    public function actualizar(Funcionario $funcionario, array $data): Funcionario
    {
        $funcionario->update($this->limpiarData($data));
        return $funcionario->fresh()->load(['region', 'cargo', 'area']);
    }

    public function eliminar(Funcionario $funcionario): void
    {
        $funcionario->delete();
    }

    public function provincias(int $idRegion)
    {
        return Provincia::where('id_region', $idRegion)->get();
    }

    public function comunas(int $idProvincia)
    {
        return Comuna::where('id_provincia', $idProvincia)->get();
    }

    // Convierte strings vacíos a null para campos opcionales
private function limpiarData(array $data): array
{
    $opcionales = [
        'email', 'telefono', 'direccion', 'avatar',
        'tipo_contrato', 'fecha_ingreso', 'fecha_fin_contrato',
        'id_region', 'id_provincia', 'id_comuna',
        'id_cargo', 'id_area', 'user_id',
    ];

    foreach ($opcionales as $campo) {
        if (isset($data[$campo]) && $data[$campo] === '') {
            $data[$campo] = null;
        }
    }

    return $data;
}

    public function destroy(Funcionario $funcionario)
{
    $this->service->eliminar($funcionario);

    return response()->json([
        'ok'      => true,
        'message' => 'Funcionario eliminado correctamente.',
    ]);
}

public function actividad(Funcionario $funcionario)
{
    return $funcionario->activities()->latest()->take(20)->get();
}

}