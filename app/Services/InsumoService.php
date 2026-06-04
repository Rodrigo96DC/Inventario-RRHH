<?php

namespace App\Services;

use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\InsumoMovimiento;
use Illuminate\Http\Request;

class InsumoService
{
    // ─── LISTADO ─────────────────────────────────────────────────
    public function listar(Request $request)
    {
        return Insumo::with('categoria')
            ->when($request->buscar, fn($q) =>
                $q->where('nombre', 'like', "%{$request->buscar}%")
            )
            ->when($request->estado, fn($q) =>
                $q->where('estado', $request->estado)
            )
            ->when($request->alerta, fn($q) =>
                $q->whereRaw('stock_actual <= stock_minimo')
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    // ─── CRUD ────────────────────────────────────────────────────
    public function crear(array $data): Insumo
    {
        return Insumo::create($data);
    }

    public function actualizar(Insumo $insumo, array $data): Insumo
    {
        $insumo->update($data);
        return $insumo->fresh();
    }

    public function toggleEstado(Insumo $insumo): Insumo
    {
        $insumo->update([
            'estado' => $insumo->estado === 'Activo' ? 'Inactivo' : 'Activo'
        ]);
        return $insumo->fresh();
    }

    // ─── ENTRADA (recepción de compra) ───────────────────────────
    public function entrada(Insumo $insumo, array $items, int $idFactura = null): void
    {
        foreach ($items as $item) {
            // Crear lote
            $lote = InsumoLote::create([
                'id_insumo'          => $insumo->id,
                'id_factura'         => $idFactura,
                'cantidad_inicial'   => $item['cantidad'],
                'cantidad_restante'  => $item['cantidad'],
                'fecha_ingreso'      => $item['fecha'],
                'fecha_vencimiento'  => $item['fecha_vencimiento'] ?? null,
                'precio_unitario'    => $item['precio_unitario'] ?? null,
                'estado'             => 'Activo',
                'created_by'         => auth()->id(),
            ]);

            // Registrar movimiento
            InsumoMovimiento::create([
                'id_insumo'    => $insumo->id,
                'id_lote'      => $lote->id,
                'id_factura'   => $idFactura,
                'tipo'         => 'Entrada',
                'cantidad'     => $item['cantidad'],
                'fecha'        => $item['fecha'],
                'observacion'  => $item['observacion'] ?? null,
                'created_by'   => auth()->id(),
            ]);

            // Actualizar stock
            $insumo->increment('stock_actual', $item['cantidad']);
        }
    }

    // ─── SALIDA (despacho a funcionario) ─────────────────────────
    public function salida(Insumo $insumo, int $cantidad, int $idFuncionario, string $fecha, string $observacion = null): bool
    {
        if ($insumo->stock_actual < $cantidad) return false;

        $restante = $cantidad;

        // FIFO: lotes más antiguos primero / LIFO: lotes más nuevos primero
        $lotes = $insumo->lotesActivos()
            ->when($insumo->metodo_valuacion === 'FIFO',
                fn($q) => $q->orderBy('fecha_ingreso', 'asc'),
                fn($q) => $q->orderBy('fecha_ingreso', 'desc')
            )
            ->get();

        foreach ($lotes as $lote) {
            if ($restante <= 0) break;

            $descontar = min($lote->cantidad_restante, $restante);

            // Registrar movimiento por lote
            InsumoMovimiento::create([
                'id_insumo'      => $insumo->id,
                'id_lote'        => $lote->id,
                'id_funcionario' => $idFuncionario,
                'tipo'           => 'Salida',
                'cantidad'       => $descontar,
                'fecha'          => $fecha,
                'observacion'    => $observacion,
                'created_by'     => auth()->id(),
            ]);

            // Actualizar lote
            $nuevaCantidad = $lote->cantidad_restante - $descontar;
            $lote->update([
                'cantidad_restante' => $nuevaCantidad,
                'estado'            => $nuevaCantidad <= 0 ? 'Agotado' : 'Activo',
            ]);

            $restante -= $descontar;
        }

        // Actualizar stock del insumo
        $insumo->decrement('stock_actual', $cantidad);

        // Notificar si stock bajo
        if ($insumo->stock_actual <= $insumo->stock_minimo) {
            \App\Models\Notificacion::create([
                'tipo'               => 'stock_minimo',
                'id_ref'             => $insumo->id,
                'fecha_ref'          => now()->toDateString(),
                'fecha_notificacion' => now()->toDateString(),
                'titulo'             => 'Stock mínimo alcanzado',
                'mensaje'            => "El insumo \"{$insumo->nombre}\" tiene stock bajo ({$insumo->stock_actual} {$insumo->unidad_medida})",
                'leido'              => false,
                'created_by'         => auth()->id(),
            ]);
        }

        return true;
    }
}