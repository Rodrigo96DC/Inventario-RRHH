<?php

namespace App\Imports;

use App\Models\ItemInventario;
use App\Models\SpecsHerramienta;

class InventarioHerramientaImport extends InventarioBaseImport
{
    protected string $nombreCategoria = 'Herramienta';

    protected function crearSpecs(ItemInventario $item, array $row): void
    {
        SpecsHerramienta::create([
            'id_item'  => $item->id,
            'tipo'     => $row['tipo'] ?? null,
            'material' => $row['material'] ?? null,
            'medida'   => $row['medida'] ?? null,
        ]);
    }
}