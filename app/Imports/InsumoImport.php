<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\InsumoMovimiento;
use App\Models\CategoriaInventario;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;

class InsumoImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    public array $resultados = [];

    public function onRow(Row $row)
    {
        $data   = $row->toArray();
        $nombre = trim((string)($data['nombre'] ?? ''));
        $fila   = $row->getIndex() + 1;

        if ($nombre === '') return;

        // Verificar duplicado
        $existe = Insumo::whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->exists();
        if ($existe) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Omitido',
                'motivo' => 'Ya existe un insumo con ese nombre',
            ];
            return;
        }

        // Buscar categoría
        $categoria = null;
        if (!empty($data['categoria'])) {
            $categoria = CategoriaInventario::whereRaw('LOWER(nombre) = ?', [
                strtolower(trim($data['categoria']))
            ])->first();
        }

        // Validar método valuación
        $metodo = strtoupper(trim($data['metodo_valuacion'] ?? 'FIFO'));
        if (!in_array($metodo, ['FIFO', 'LIFO'])) $metodo = 'FIFO';

        try {
            $insumo = Insumo::create([
                'nombre'           => $nombre,
                'descripcion'      => trim($data['descripcion'] ?? '') ?: null,
                'unidad_medida'    => trim($data['unidad_medida'] ?? 'unidad'),
                'stock_minimo'     => (int)($data['stock_minimo'] ?? 0),
                'stock_actual'     => 0,
                'metodo_valuacion' => $metodo,
                'id_categoria'     => $categoria?->id,
                'estado'           => 'Activo',
            ]);

            // Crear lote inicial si hay cantidad
            $cantidad = (int)($data['cantidad_inicial'] ?? 0);
            if ($cantidad > 0) {
                $fecha = $this->limpiarFecha($data['fecha_ingreso'] ?? null) ?? now()->toDateString();

                $lote = InsumoLote::create([
                    'id_insumo'         => $insumo->id,
                    'cantidad_inicial'  => $cantidad,
                    'cantidad_restante' => $cantidad,
                    'fecha_ingreso'     => $fecha,
                    'precio_unitario'   => $data['precio_unitario'] ?? null,
                    'estado'            => 'Activo',
                    'created_by'        => auth()->id(),
                ]);

                InsumoMovimiento::create([
                    'id_insumo'  => $insumo->id,
                    'id_lote'    => $lote->id,
                    'tipo'       => 'Entrada',
                    'cantidad'   => $cantidad,
                    'fecha'      => $fecha,
                    'observacion'=> 'Carga masiva inicial',
                    'created_by' => auth()->id(),
                ]);

                $insumo->update(['stock_actual' => $cantidad]);
            }

            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Importado',
                'motivo' => "Stock inicial: {$cantidad}",
            ];

        } catch (\Exception $e) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Error',
                'motivo' => $e->getMessage(),
            ];
        }
    }

    protected function limpiarFecha($fecha): ?string
    {
        if (!$fecha) return null;
        if (is_numeric($fecha)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)->format('Y-m-d');
            } catch (\Exception $e) { return null; }
        }
        try {
            return \Carbon\Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }
}