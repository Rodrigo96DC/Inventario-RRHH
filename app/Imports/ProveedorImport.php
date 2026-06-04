<?php

namespace App\Imports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;

class ProveedorImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    public array $resultados = [];

    public function onRow(Row $row)
    {
        $data   = $row->toArray();
        $rut    = isset($data['rut']) ? (string) $data['rut'] : null;
        $nombre = trim((string)($data['razon_social'] ?? ''));
        $fila   = $row->getIndex() + 1;

        if (!$rut || $nombre === '') return;

        $existe = Proveedor::whereRaw('REPLACE(rut, ".", "") = ?', [
            str_replace('.', '', $rut)
        ])->exists();

        if ($existe) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Omitido',
                'motivo' => "RUT {$rut} ya existe",
            ];
            return;
        }

        try {
            Proveedor::create([
                'razon_social' => $nombre,
                'rut'          => $rut,
                'contacto'     => $data['contacto'] ?? null,
                'telefono'     => isset($data['telefono']) ? (string) $data['telefono'] : null,
                'email'        => $data['email'] ?? null,
                'direccion'    => $data['direccion'] ?? null,
                'estado'       => 'Activo',
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