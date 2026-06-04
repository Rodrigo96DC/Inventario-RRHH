<?php

namespace App\Imports;

use App\Models\ItemInventario;
use App\Models\SpecsComputacional;

class InventarioComputacionalImport extends InventarioBaseImport
{
    protected string $nombreCategoria = 'Computacional';

    protected function crearSpecs(ItemInventario $item, array $row): void
    {
        SpecsComputacional::create([
            'id_item'          => $item->id,
            'procesador'       => $row['procesador'] ?? null,
            'ram'              => $row['ram'] ?? null,
            'tipo_disco'       => $row['tipo_disco'] ?? null,
            'capacidad_disco'  => $row['capacidad_disco'] ?? null,
            'mac'              => $row['mac'] ?? null,
            'ip'               => $row['ip'] ?? null,
            'hostname'         => $row['hostname'] ?? null,
            'numero_inventario'=> $row['numero_inventario'] ?? null,
            'entradas'         => $row['entradas'] ?? null,
        ]);
    }
}