<?php

namespace App\Services;

use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Puesto;
use App\Models\PuestoFuncionario;
use Illuminate\Support\Facades\Storage;
use App\Models\HistorialPiso; 

class UbicacionService
{
    // ─── EDIFICIOS ───────────────────────────────────────────────
    public function listarEdificios()
    {
        return Edificio::withCount('pisos')->get();
    }

    public function crearEdificio(array $data): Edificio
    {
        return Edificio::create($data);
    }

    public function actualizarEdificio(Edificio $edificio, array $data): Edificio
    {
        $edificio->update($data);
        return $edificio->fresh();
    }

    // ─── PISOS ───────────────────────────────────────────────────
    public function crearPiso(array $data, $imagen = null): Piso
    {
        if ($imagen) {
            $data['imagen'] = $imagen->store('planos', 'public');
        }
        return Piso::create($data);
    }

    public function actualizarPiso(Piso $piso, array $data, $imagen = null): Piso
    {
        if ($imagen) {
            if ($piso->imagen) Storage::disk('public')->delete($piso->imagen);
            $data['imagen'] = $imagen->store('planos', 'public');
        }
        $piso->update($data);
        return $piso->fresh();
    }

    // ─── PUESTOS ─────────────────────────────────────────────────
    public function crearPuesto(array $data): Puesto
    {
        return Puesto::create($data);
    }

    public function actualizarPuesto(Puesto $puesto, array $data): Puesto
    {
        $puesto->update($data);
        return $puesto->fresh();
    }

    public function eliminarPuesto(Puesto $puesto): void
    {
        $puesto->delete();
    }

    public function puestosConFuncionario(Piso $piso): array
    {
        return $piso->puestos()
            ->with(['asignacionActiva.funcionario.cargo', 'asignacionActiva.funcionario.area'])
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'numero'     => $p->numero,
                'seccion'    => $p->seccion,
                'x1'         => $p->x1,
                'y1'         => $p->y1,
                'x2'         => $p->x2,
                'y2'         => $p->y2,
                'estado'     => $p->estado,
                'funcionario'=> $p->asignacionActiva?->funcionario ? [
                    'id'       => $p->asignacionActiva->funcionario->id,
                    'nombre'   => $p->asignacionActiva->funcionario->nombre_completo,
                    'cargo'    => $p->asignacionActiva->funcionario->cargo->nombre ?? '—',
                    'area'     => $p->asignacionActiva->funcionario->area->nombre ?? '—',
                    'iniciales'=> $p->asignacionActiva->funcionario->iniciales,
                ] : null,
            ])
            ->toArray();
    }

    // ─── ASIGNACIONES ────────────────────────────────────────────
    public function asignarFuncionario(Puesto $puesto, array $data): PuestoFuncionario
    {
        // Liberar asignación activa anterior si existe
        PuestoFuncionario::where('id_puesto', $puesto->id)
            ->where('estado', 'Activo')
            ->update(['estado' => 'Liberado', 'fecha_fin' => now()->toDateString()]);

        $puesto->update(['estado' => 'Ocupado']);

        return PuestoFuncionario::create([
            'id_puesto'      => $puesto->id,
            'id_funcionario' => $data['id_funcionario'],
            'fecha_inicio'   => $data['fecha_inicio'],
            'estado'         => 'Activo',
            'observacion'    => $data['observacion'] ?? null,
        ]);
    }

    public function liberarPuesto(Puesto $puesto): void
    {
        PuestoFuncionario::where('id_puesto', $puesto->id)
            ->where('estado', 'Activo')
            ->update(['estado' => 'Liberado', 'fecha_fin' => now()->toDateString()]);

        $puesto->update(['estado' => 'Disponible']);
    }

    public function limpiarPiso(Piso $piso, string $motivo): HistorialPiso
{
    // 1. Guardar snapshot completo antes de limpiar
    $puestos = $piso->puestos()
        ->with(['asignaciones' => fn($q) => $q->where('estado', 'Activo')])
        ->get();

    $snapshotPuestos = $puestos->map(fn($p) => [
        'id'      => $p->id,
        'numero'  => $p->numero,
        'seccion' => $p->seccion,
        'x1'      => $p->x1,
        'y1'      => $p->y1,
        'x2'      => $p->x2,
        'y2'      => $p->y2,
        'estado'  => $p->estado,
    ])->toArray();

    $snapshotAsignaciones = $puestos->flatMap(fn($p) =>
        $p->asignaciones->map(fn($a) => [
            'id_puesto'      => $a->id_puesto,
            'id_funcionario' => $a->id_funcionario,
            'fecha_inicio'   => $a->fecha_inicio?->toDateString(),
            'fecha_fin'      => $a->fecha_fin?->toDateString(),
            'observacion'    => $a->observacion,
        ])
    )->toArray();

    // 2. Guardar historial
    $historial = HistorialPiso::create([
        'id_piso'               => $piso->id,
        'snapshot_puestos'      => $snapshotPuestos,
        'snapshot_asignaciones' => $snapshotAsignaciones,
        'motivo'                => $motivo,
        'created_by'            => auth()->id(),
    ]);

    // 3. Liberar asignaciones activas
    PuestoFuncionario::whereIn('id_puesto', $puestos->pluck('id'))
        ->where('estado', 'Activo')
        ->update([
            'estado'   => 'Liberado',
            'fecha_fin'=> now()->toDateString(),
        ]);

    // 4. Eliminar puestos
    $piso->puestos()->delete();

    return $historial;
}

public function rollbackPiso(HistorialPiso $historial): void
{
    $piso = $historial->piso;

    // Eliminar puestos actuales
    $piso->puestos()->delete();

    // Restaurar puestos del snapshot
    foreach ($historial->snapshot_puestos as $p) {
        Puesto::create([
            'id_piso'    => $piso->id,
            'numero'     => $p['numero'],
            'seccion'    => $p['seccion'],
            'x1'         => $p['x1'],
            'y1'         => $p['y1'],
            'x2'         => $p['x2'],
            'y2'         => $p['y2'],
            'estado'     => 'Disponible',
            'created_by' => auth()->id(),
        ]);
    }

    // Marcar historial como revertido
    $historial->update([
        'revertido'    => true,
        'revertido_at' => now(),
        'revertido_by' => auth()->id(),
    ]);
}

public function historialPiso(Piso $piso)
{
    return HistorialPiso::where('id_piso', $piso->id)
        ->with(['creadoPor', 'revertidoPor'])
        ->latest()
        ->get();
}
}