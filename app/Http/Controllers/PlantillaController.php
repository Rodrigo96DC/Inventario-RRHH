<?php

namespace App\Http\Controllers;

use App\Imports\AreaImport;
use App\Imports\CargoImport;
use App\Imports\CategoriaImport;
use App\Imports\FuncionarioImport;
use App\Imports\InventarioComputacionalImport;
use App\Imports\InventarioHerramientaImport;
use App\Imports\InventarioMobiliarioImport;
use App\Imports\InventarioVehiculoImport;
use App\Imports\ProveedorImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlantillaController extends Controller
{
    // ─── DESCARGAR PLANTILLAS ────────────────────────────────────
    public function descargar(string $tipo)
    {
        $plantillas = [
            'cargos' => [
                'nombre' => 'plantilla_cargos.xlsx',
                'columnas' => ['nombre', 'grado'],
                'ejemplos' => [
                    ['Profesional', '10'],
                    ['Técnico', '8'],
                    ['Administrativo', '6'],
                ],
            ],
            'areas' => [
                'nombre' => 'plantilla_areas.xlsx',
                'columnas' => ['nombre'],
                'ejemplos' => [
                    ['Departamento de TI'],
                    ['Recursos Humanos'],
                    ['Finanzas'],
                ],
            ],
            'categorias' => [
                'nombre' => 'plantilla_categorias.xlsx',
                'columnas' => ['nombre', 'icono'],
                'ejemplos' => [
                    ['Computacional', 'ti-cpu'],
                    ['Mobiliario', 'ti-armchair'],
                ],
            ],

            'funcionarios' => [
                'nombre' => 'plantilla_funcionarios.xlsx',
                'columnas' => ['rut', 'nombre', 'apellido', 'email', 'telefono', 'direccion', 'fecha_ingreso', 'tipo_contrato', 'estado', 'area', 'cargo'],
                'ejemplos' => [
                    ['12.345.678-9', 'Juan', 'González', 'juan@gmail.com', '+56912345678', 'Av. Principal 123', '01-01-2024', 'Contrata', 'Activo', 'Departamento de TI', 'Profesional'],
                    ['9.876.543-2',  'María', 'Pérez',   'maria@gmail.com', '+56987654321', 'Calle Sur 456',    '15-03-2023', 'Planta',   'Activo', 'Recursos Humanos',   'Técnico'],
                ],
            ],

            'proveedores' => [
                'nombre' => 'plantilla_proveedores.xlsx',
                'columnas' => ['razon_social', 'rut', 'contacto', 'telefono', 'email', 'direccion'],
                'ejemplos' => [
                    ['Empresa Ejemplo SpA', '76.123.456-7', 'Juan Pérez', '+56912345678', 'contacto@empresa.cl', 'Av. Principal 123'],
                    ['Proveedor Demo Ltda', '77.987.654-3', 'María López', '+56987654321', 'info@demo.cl', 'Calle Sur 456'],
                ],
            ],
            'inventario_computacional' => [
                'nombre' => 'plantilla_inventario_computacional.xlsx',
                'columnas' => [
                    'nombre', 'marca', 'modelo', 'codigo_interno', 'numero_serie',
                    'precio', 'fecha_adquisicion', 'ubicacion', 'estado', 'observacion',
                    'procesador', 'ram', 'tipo_disco', 'capacidad_disco',
                    'mac', 'ip', 'hostname', 'numero_inventario', 'entradas',
                ],
                'ejemplos' => [
                    [
                        'Computador HP', 'HP', '280 G9', 'COD-001', 'CNC4125D3',
                        '550000', '2026-01-15', 'Oficina 3', 'Disponible', '',
                        'Intel Core i5', '8GB', 'SSD', '256GB',
                        'AA:BB:CC:DD:EE:FF', '192.168.1.10', 'PC-001', 'INV-001', 'USB,HDMI',
                    ],
                ],
            ],
            'inventario_mobiliario' => [
                'nombre' => 'plantilla_inventario_mobiliario.xlsx',
                'columnas' => [
                    'nombre', 'marca', 'modelo', 'codigo_interno', 'numero_serie',
                    'precio', 'fecha_adquisicion', 'ubicacion', 'estado', 'observacion',
                    'material', 'color', 'dimensiones',
                ],
                'ejemplos' => [
                    [
                        'Silla ergonómica', 'Oken', 'Modelo A', 'COD-002', '',
                        '120000', '2026-01-15', 'Oficina 1', 'Disponible', '',
                        'Plástico/Tela', 'Negro', '60x60x100cm',
                    ],
                ],
            ],
            'inventario_vehiculo' => [
                'nombre' => 'plantilla_inventario_vehiculo.xlsx',
                'columnas' => [
                    'nombre', 'marca', 'modelo', 'codigo_interno', 'numero_serie',
                    'precio', 'fecha_adquisicion', 'ubicacion', 'estado', 'observacion',
                    'patente', 'anio', 'tipo_combustible', 'kilometraje',
                    'numero_motor', 'numero_chasis',
                ],
                'ejemplos' => [
                    [
                        'Camioneta', 'Toyota', 'Hilux', 'COD-003', 'ABC123',
                        '15000000', '2026-01-15', 'Estacionamiento', 'Disponible', '',
                        'AB-CD-12', '2023', 'Diesel', '15000',
                        'MOT-001', 'CHA-001',
                    ],
                ],
            ],
            'inventario_herramienta' => [
                'nombre' => 'plantilla_inventario_herramienta.xlsx',
                'columnas' => [
                    'nombre', 'marca', 'modelo', 'codigo_interno', 'numero_serie',
                    'precio', 'fecha_adquisicion', 'ubicacion', 'estado', 'observacion',
                    'tipo', 'material', 'medida',
                ],
                'ejemplos' => [
                    [
                        'Taladro', 'Bosch', 'GSB 13', 'COD-004', 'BSH-001',
                        '85000', '2026-01-15', 'Bodega', 'Disponible', '',
                        'Eléctrica', 'Metal/Plástico', '13mm',
                    ],
                ],
            ],

            'insumos' => [
    'nombre'   => 'plantilla_insumos.xlsx',
    'columnas' => [
        'nombre', 'descripcion', 'unidad_medida', 'stock_minimo',
        'metodo_valuacion', 'categoria', 'cantidad_inicial',
        'precio_unitario', 'fecha_ingreso'
    ],
    'ejemplos' => [
        ['Papel A4', 'Resma papel A4 75gr', 'resma', '5', 'FIFO', 'Papel', '100', '3500', '2026-01-15'],
        ['Tóner HP', 'Tóner impresora HP LaserJet', 'unidad', '2', 'FIFO', 'Tóner', '10', '25000', '2026-01-15'],
        ['Lápiz BIC', 'Lápiz pasta negro', 'caja', '3', 'LIFO', 'Lapiz Pasta', '20', '1500', '2026-01-15'],
    ],
],
        ];

        if (! isset($plantillas[$tipo])) {
            abort(404);
        }

        $config = $plantillas[$tipo];
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos del header
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a56db']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Columnas
        foreach ($config['columnas'] as $i => $col) {
            $letra = chr(65 + $i);
            $sheet->setCellValue("{$letra}1", strtoupper($col));
            $sheet->getColumnDimension($letra)->setWidth(25);
        }

        // Filas de ejemplo en gris claro
        foreach ($config['ejemplos'] as $fila => $valores) {
            foreach ($valores as $col => $valor) {
                $letra = chr(65 + $col);
                $sheet->setCellValue("{$letra}".($fila + 2), $valor);
            }
            $sheet->getStyle('A'.($fila + 2).':Z'.($fila + 2))
                ->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f1f5f9']],
                    'font' => ['color' => ['rgb' => '94a3b8']],
                ]);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$config['nombre']}\"",
        ]);
    }

    // ─── IMPORTAR ────────────────────────────────────────────────
public function importar(Request $request, string $tipo)
{
    $request->validate([
        'archivo' => 'required|file|mimes:xlsx,xls|max:5120',
    ]);

    $imports = [
        'cargos'                   => \App\Imports\CargoImport::class,
        'areas'                    => \App\Imports\AreaImport::class,
        'categorias'               => \App\Imports\CategoriaImport::class,
        'funcionarios'             => \App\Imports\FuncionarioImport::class,
        'proveedores'              => \App\Imports\ProveedorImport::class,
        'inventario_computacional' => \App\Imports\InventarioComputacionalImport::class,
        'inventario_mobiliario'    => \App\Imports\InventarioMobiliarioImport::class,
        'inventario_vehiculo'      => \App\Imports\InventarioVehiculoImport::class,
        'inventario_herramienta'   => \App\Imports\InventarioHerramientaImport::class,
        'insumos' => \App\Imports\InsumoImport::class,
        ];

    if (!isset($imports[$tipo])) {
        return response()->json(['ok' => false, 'message' => 'Tipo no válido.'], 422);
    }

    $import = new $imports[$tipo];
    \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('archivo'));

    // Si es inventario generar reporte
    // Generar reporte para todos los tipos que tengan resultados
if (isset($import->resultados)) {
        $resultados  = $import->resultados;
        $importados  = collect($resultados)->where('estado', 'Importado')->count();
        $omitidos    = collect($resultados)->where('estado', 'Omitido')->count();
        $errores     = collect($resultados)->where('estado', 'Error')->count();

        // Generar Excel de resultados
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resultados');

        // Headers
        $sheet->setCellValue('A1', 'Fila');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Estado');
        $sheet->setCellValue('D1', 'Motivo');

        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a56db']],
        ]);

        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        foreach ($resultados as $i => $r) {
            $fila  = $i + 2;
            $color = match($r['estado']) {
                'Importado' => 'd1fae5',
                'Omitido'   => 'fef3c7',
                'Error'     => 'fee2e2',
                default     => 'ffffff',
            };

            $sheet->setCellValue("A{$fila}", $r['fila']);
            $sheet->setCellValue("B{$fila}", $r['nombre']);
            $sheet->setCellValue("C{$fila}", $r['estado']);
            $sheet->setCellValue("D{$fila}", $r['motivo']);

            $sheet->getStyle("A{$fila}:D{$fila}")->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $color]],
            ]);
        }

        $nombreArchivo = "resultado_importacion_{$tipo}_" . date('Ymd_His') . ".xlsx";
        $writer        = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Guardar temporalmente
        $rutaTemporal = storage_path("app/temp_{$nombreArchivo}");
        $writer->save($rutaTemporal);

        // Retornar URL de descarga
        return response()->json([
            'ok'         => true,
            'message'    => "Importación completada: {$importados} importados, {$omitidos} omitidos, {$errores} errores.",
            'reporte'    => "/plantillas/reporte/{$nombreArchivo}",
            'importados' => $importados,
            'omitidos'   => $omitidos,
            'errores'    => $errores,
        ]);
    }

    return response()->json([
        'ok'      => true,
        'message' => 'Importación completada correctamente.',
    ]);
}
}
