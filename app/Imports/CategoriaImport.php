<?php

namespace App\Imports;

use App\Models\CategoriaInventario;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;

class CategoriaImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    public array $resultados = [];

    public function onRow(Row $row)
    {
        $data   = $row->toArray();
        $nombre = trim((string)($data['nombre'] ?? ''));
        $fila   = $row->getIndex() + 1;

        if ($nombre === '') return;

        $existe = CategoriaInventario::whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->exists();
        if ($existe) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Omitido',
                'motivo' => 'Ya existe una categoría con ese nombre',
            ];
            return;
        }

        try {
            CategoriaInventario::create([
                'nombre' => $nombre,
                'icono'  => $data['icono'] ?? null,
                'estado' => 'Activo',
            ]);

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
}