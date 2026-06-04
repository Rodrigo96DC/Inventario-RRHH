<?php

namespace App\Imports;

use App\Models\ItemInventario;
use App\Models\SpecsVehiculo;

class InventarioVehiculoImport extends InventarioBaseImport
{
    protected string $nombreCategoria = 'Vehículo';

    protected function crearSpecs(ItemInventario $item, array $row): void
    {
        SpecsVehiculo::create([
            'id_item'        => $item->id,
            'patente'        => $row['patente'] ?? null,
            'anio'           => $row['anio'] ?? null,
            'tipo_combustible'=> $row['tipo_combustible'] ?? null,
            'kilometraje'    => $row['kilometraje'] ?? null,
            'numero_motor'   => $row['numero_motor'] ?? null,
            'numero_chasis'  => $row['numero_chasis'] ?? null,
        ]);
    }
}