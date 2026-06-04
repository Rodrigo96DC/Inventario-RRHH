<?php

namespace App\Imports;

use App\Models\Funcionario;
use App\Models\Area;
use App\Models\Cargo;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;

class FuncionarioImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    public array $resultados = [];

    public function onRow(Row $row)
    {
        $data  = $row->toArray();
        $rut   = isset($data['rut']) ? (string) $data['rut'] : null;
        $fila  = $row->getIndex() + 1;
        $nombre = trim(($data['nombre'] ?? '') . ' ' . ($data['apellido'] ?? ''));

        if (!$rut || trim($nombre) === '') return;

        $rut = $this->limpiarRut($rut);
        if (!$rut) {
            $this->resultados[] = [
                'fila'   => $fila,
                'nombre' => $nombre,
                'estado' => 'Error',
                'motivo' => 'RUT inválido',
            ];
            return;
        }

        $existe = Funcionario::whereRaw("REPLACE(rut, '.', '') = ?", [
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

        $area  = Area::whereRaw('LOWER(nombre) = ?', [strtolower($data['area'] ?? '')])->first();
        $cargo = Cargo::whereRaw('LOWER(nombre) = ?', [strtolower($data['cargo'] ?? '')])->first();

        $tiposValidos = ['Planta', 'Contrata', 'Honorarios'];
        $tipoContrato = null;
        foreach ($tiposValidos as $tipo) {
            if (strtolower($data['tipo_contrato'] ?? '') === strtolower($tipo)) {
                $tipoContrato = $tipo;
                break;
            }
        }

        $estado = 'Activo';
        foreach (['Activo', 'Inactivo'] as $est) {
            if (strtolower($data['estado'] ?? '') === strtolower($est)) {
                $estado = $est;
                break;
            }
        }

        try {
            Funcionario::create([
                'rut'           => $rut,
                'nombre'        => $data['nombre'] ?? null,
                'apellido'      => $data['apellido'] ?? null,
                'email'         => $data['email'] ?? null,
                'telefono'      => $data['telefono'] ?? null,
                'direccion'     => $data['direccion'] ?? null,
                'fecha_ingreso' => $this->limpiarFecha($data['fecha_ingreso'] ?? null),
                'tipo_contrato' => $tipoContrato,
                'estado'        => $estado,
                'id_area'       => $area?->id,
                'id_cargo'      => $cargo?->id,
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

    private function limpiarRut($rut): ?string
    {
        if (!$rut) return null;
        $rut   = (string) $rut;
        $clean = preg_replace('/[^0-9kK]/', '', $rut);
        if (strlen($clean) < 7) return null;
        $cuerpo = substr($clean, 0, -1);
        $dv     = strtoupper(substr($clean, -1));
        $cuerpo = number_format((int)$cuerpo, 0, '', '.');
        return "{$cuerpo}-{$dv}";
    }

    private function limpiarFecha($fecha): ?string
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