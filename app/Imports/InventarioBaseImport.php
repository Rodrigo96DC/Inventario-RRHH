<?php

namespace App\Imports;

use App\Models\ItemInventario;
use App\Models\CategoriaInventario;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Log;

abstract class InventarioBaseImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    protected string $nombreCategoria;
    public array $resultados = [];

    public function onRow(Row $row)
    {
        $data   = $row->toArray();
        $nombre = trim((string)($data['nombre'] ?? ''));
        $fila   = $row->getIndex() + 1;

        if ($nombre === '') return;

        // Verificar duplicado por código interno
        if (!empty($data['codigo_interno'])) {
            $existe = ItemInventario::where('codigo_interno', trim($data['codigo_interno']))->exists();
            if ($existe) {
                $this->resultados[] = [
                    'fila'   => $fila,
                    'nombre' => $nombre,
                    'estado' => 'Omitido',
                    'motivo' => "Código interno '{$data['codigo_interno']}' ya existe",
                ];
                return;
            }
        }

        // Verificar duplicado por número de serie
        if (!empty($data['numero_serie'])) {
            $existe = ItemInventario::where('numero_serie', trim($data['numero_serie']))->exists();
            if ($existe) {
                $this->resultados[] = [
                    'fila'   => $fila,
                    'nombre' => $nombre,
                    'estado' => 'Omitido',
                    'motivo' => "N° serie '{$data['numero_serie']}' ya existe",
                ];
                return;
            }
        }

        // Buscar categoría
        $categoria = CategoriaInventario::whereRaw('LOWER(nombre) = ?', [
            strtolower($this->nombreCategoria)
        ])->first();

        if (!$categoria) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Error',
                'motivo' => "Categoría '{$this->nombreCategoria}' no encontrada",
            ];
            return;
        }

        try {
            $item = ItemInventario::create([
                'id_categoria'     => $categoria->id,
                'nombre'           => $nombre,
                'marca'            => trim($data['marca'] ?? '') ?: null,
                'modelo'           => trim($data['modelo'] ?? '') ?: null,
                'codigo_interno'   => trim($data['codigo_interno'] ?? '') ?: null,
                'numero_serie'     => trim($data['numero_serie'] ?? '') ?: null,
                'precio'           => $data['precio'] ?? null,
                'fecha_adquisicion'=> $this->limpiarFecha($data['fecha_adquisicion'] ?? null),
                'ubicacion'        => trim($data['ubicacion'] ?? '') ?: null,
                'estado'           => $this->limpiarEstado($data['estado'] ?? 'Disponible'),
                'observacion'      => trim($data['observacion'] ?? '') ?: null,
            ]);

            $this->crearSpecs($item, $data);

            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Importado',
                'motivo' => '—',
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

    abstract protected function crearSpecs(ItemInventario $item, array $row): void;

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

    protected function limpiarEstado($estado): string
    {
        $validos = ['Disponible', 'Asignado', 'En mantención', 'De baja', 'Extraviado'];
        foreach ($validos as $v) {
            if (strtolower(trim($estado)) === strtolower($v)) return $v;
        }
        return 'Disponible';
    }
}