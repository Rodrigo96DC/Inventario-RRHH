<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaInventario;

class CategoriasAdicionalesSeeder extends Seeder
{
   public function run(): void
{
    $categorias = [
        ['nombre' => 'Notebook',        'icono' => 'ti-device-laptop'],
        ['nombre' => 'Desktop',         'icono' => 'ti-device-desktop'],
        ['nombre' => 'Servidor',        'icono' => 'ti-server'],
        ['nombre' => 'Monitor',         'icono' => 'ti-device-tv'],
        ['nombre' => 'Impresora',       'icono' => 'ti-printer'],
        ['nombre' => 'Escáner',         'icono' => 'ti-scan'],
        ['nombre' => 'Proyector',       'icono' => 'ti-device-projector'],
        ['nombre' => 'Tablet',          'icono' => 'ti-device-tablet'],
        ['nombre' => 'Teléfono',        'icono' => 'ti-phone'],
        ['nombre' => 'Red',             'icono' => 'ti-network'],
        ['nombre' => 'UPS',             'icono' => 'ti-battery'],
        ['nombre' => 'Teclado',         'icono' => 'ti-keyboard'],
        ['nombre' => 'Mouse',           'icono' => 'ti-mouse'],
        ['nombre' => 'Audífono',        'icono' => 'ti-headphones'],
        ['nombre' => 'Cámara',          'icono' => 'ti-camera'],
        ['nombre' => 'Silla',           'icono' => 'ti-armchair'],
        ['nombre' => 'Escritorio',      'icono' => 'ti-table'],
        ['nombre' => 'Archivador',      'icono' => 'ti-archive'],
        ['nombre' => 'Estante',         'icono' => 'ti-layout-rows'],
        ['nombre' => 'Locker',          'icono' => 'ti-lock'],
        ['nombre' => 'Automóvil',       'icono' => 'ti-car'],
        ['nombre' => 'Camioneta',       'icono' => 'ti-truck'],
        ['nombre' => 'Motocicleta',     'icono' => 'ti-motorbike'],
        ['nombre' => 'Eléctrica',       'icono' => 'ti-bolt'],
        ['nombre' => 'Manual',          'icono' => 'ti-tool'],
        ['nombre' => 'Medición',        'icono' => 'ti-ruler'],
        ['nombre' => 'Papel',           'icono' => 'ti-file'],
        ['nombre' => 'Tóner',           'icono' => 'ti-droplet'],
        ['nombre' => 'Cable',           'icono' => 'ti-plug'],
        ['nombre' => 'Limpieza',        'icono' => 'ti-brush'],
    ];

    foreach ($categorias as $cat) {
        // Usar insert directo para evitar el trait de trazabilidad
        \DB::table('categorias_inventario')->insertOrIgnore([
            'nombre'     => $cat['nombre'],
            'icono'      => $cat['icono'],
            'estado'     => 'Activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->command->info('✅ Categorías adicionales creadas.');
}
}