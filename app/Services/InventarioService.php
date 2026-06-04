<?php

namespace App\Services;

use App\Models\ItemInventario;
use App\Models\SpecsComputacional;
use App\Models\SpecsMobiliario;
use App\Models\SpecsVehiculo;
use App\Models\SpecsHerramienta;
use App\Models\SpecsInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarioService
{
 public function listar(Request $request)
{
    return ItemInventario::with(['categoria', 'asignacionActiva.funcionario'])
        ->when($request->buscar, fn($q) =>
            $q->where('nombre', 'like', "%{$request->buscar}%")
              ->orWhere('codigo_interno', 'like', "%{$request->buscar}%")
              ->orWhere('marca', 'like', "%{$request->buscar}%")
              ->orWhere('modelo', 'like', "%{$request->buscar}%")
              ->orWhere('numero_serie', 'like', "%{$request->buscar}%")
        )
        ->when($request->id_categoria, fn($q) =>
            $q->where('id_categoria', $request->id_categoria)
              ->orWhereHas('categorias', fn($q2) =>
                  $q2->where('categorias_inventario.id', $request->id_categoria)
              )
        )
        ->when($request->estado, fn($q) =>
            $q->where('estado', $request->estado)
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();
}

    public function crear(array $data, $imagen = null): ItemInventario
    {
        if ($imagen) {
            $data['imagen'] = $imagen->store('inventario', 'public');
        }

        $item = ItemInventario::create($this->limpiarData($data));
        $this->guardarSpecs($item, $data);

        return $item;
    }

    public function actualizar(ItemInventario $item, array $data, $imagen = null): ItemInventario
    {
        if ($imagen) {
            if ($item->imagen) Storage::disk('public')->delete($item->imagen);
            $data['imagen'] = $imagen->store('inventario', 'public');
        }

        $item->update($this->limpiarData($data));
        $this->guardarSpecs($item, $data);

        return $item->fresh()->load(['categoria', 'asignacionActiva.funcionario']);
    }

    public function eliminar(ItemInventario $item): void
    {
        if ($item->imagen) Storage::disk('public')->delete($item->imagen);
        $item->delete();
    }

    private function guardarSpecs(ItemInventario $item, array $data): void
    {
        $categoria = $item->categoria->nombre;

        match($categoria) {
            'Computacional' => SpecsComputacional::updateOrCreate(
                ['id_item' => $item->id],
                array_filter([
                    'procesador'         => $data['procesador'] ?? null,
                    'ram'                => $data['ram'] ?? null,
                    'tipo_disco'         => $data['tipo_disco'] ?? null,
                    'capacidad_disco'    => $data['capacidad_disco'] ?? null,
                    'mac'                => $data['mac'] ?? null,
                    'ip'                 => $data['ip'] ?? null,
                    'hostname'           => $data['hostname'] ?? null,
                    'numero_inventario'  => $data['numero_inventario'] ?? null,
                    'entradas'           => $data['entradas'] ?? null,
                ], fn($v) => !is_null($v))
            ),
            'Mobiliario' => SpecsMobiliario::updateOrCreate(
                ['id_item' => $item->id],
                array_filter([
                    'material'   => $data['material'] ?? null,
                    'color'      => $data['color'] ?? null,
                    'dimensiones'=> $data['dimensiones'] ?? null,
                ], fn($v) => !is_null($v))
            ),
            'Vehículo' => SpecsVehiculo::updateOrCreate(
                ['id_item' => $item->id],
                array_filter([
                    'patente'         => $data['patente'] ?? null,
                    'anio'            => $data['anio'] ?? null,
                    'tipo_combustible'=> $data['tipo_combustible'] ?? null,
                    'numero_motor'    => $data['numero_motor'] ?? null,
                    'numero_chasis'   => $data['numero_chasis'] ?? null,
                    'kilometraje'     => $data['kilometraje'] ?? null,
                ], fn($v) => !is_null($v))
            ),
            'Herramienta' => SpecsHerramienta::updateOrCreate(
                ['id_item' => $item->id],
                array_filter([
                    'tipo'     => $data['tipo'] ?? null,
                    'material' => $data['material'] ?? null,
                    'medida'   => $data['medida'] ?? null,
                ], fn($v) => !is_null($v))
            ),
            'Insumo' => SpecsInsumo::updateOrCreate(
                ['id_item' => $item->id],
                array_filter([
                    'stock_inicial' => $data['stock_inicial'] ?? null,
                    'stock_actual'  => $data['stock_actual'] ?? null,
                    'unidad_medida' => $data['unidad_medida'] ?? null,
                    'stock_minimo'  => $data['stock_minimo'] ?? null,
                ], fn($v) => !is_null($v))
            ),
            default => null,
        };
    }

    private function limpiarData(array $data): array
    {
        $opcionales = [
            'id_proveedor', 'id_factura', 'codigo_interno', 'marca', 'modelo',
            'numero_serie', 'precio', 'fecha_adquisicion', 'imagen',
            'ubicacion', 'observacion',
        ];

        foreach ($opcionales as $campo) {
            if (isset($data[$campo]) && $data[$campo] === '') {
                $data[$campo] = null;
            }
        }

        return $data;
    }
}