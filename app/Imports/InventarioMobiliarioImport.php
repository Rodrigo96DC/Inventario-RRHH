<?php

namespace App\Imports;

use App\Models\ItemInventario;
use App\Models\SpecsMobiliario;

class InventarioMobiliarioImport extends InventarioBaseImport
{
    protected string $nombreCategoria = 'Mobiliario';

    protected function crearSpecs(ItemInventario $item, array $row): void
    {
        SpecsMobiliario::create([
            'id_item'    => $item->id,
            'material'   => $row['material'] ?? null,
            'color'      => $row['color'] ?? null,
            'dimensiones'=> $row['dimensiones'] ?? null,
        ]);
    }
}