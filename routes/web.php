<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\CategoriaInventarioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MermaController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UbicacionController;
use App\Models\Notificacion;
  use App\Http\Controllers\AuditoriaController;
  use App\Http\Controllers\InsumoController;

use Illuminate\Support\Facades\Route;

// ─── RUTAS PÚBLICAS ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ─── LOGOUT ──────────────────────────────────────────────────────
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── RUTAS PROTEGIDAS ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Configuración ─────────────────────────────────────────────
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion/tema', [ConfiguracionController::class, 'setTema']);
    Route::post('/configuracion/color', [ConfiguracionController::class, 'setColor']);

    // ── Funcionarios ──────────────────────────────────────────────
    Route::get('funcionarios/provincias', [FuncionarioController::class, 'provincias'])->name('funcionarios.provincias');
    Route::get('funcionarios/comunas', [FuncionarioController::class, 'comunas'])->name('funcionarios.comunas');
    Route::get('funcionarios/{funcionario}/perfil', [FuncionarioController::class, 'perfil'])->name('funcionarios.perfil');
    Route::patch('funcionarios/{funcionario}/estado', [FuncionarioController::class, 'cambiarEstado'])->name('funcionarios.estado');
    Route::resource('funcionarios', FuncionarioController::class);

    // ── Cargos ────────────────────────────────────────────────────
    Route::patch('cargos/{cargo}/estado', [CargoController::class, 'toggleEstado'])->name('cargos.estado');
    Route::resource('cargos', CargoController::class);

    // ── Áreas ─────────────────────────────────────────────────────
    Route::patch('areas/{area}/estado', [AreaController::class, 'toggleEstado'])->name('areas.estado');
    Route::resource('areas', AreaController::class);

    // ── Categorías inventario ─────────────────────────────────────
    Route::patch('categorias/{categoria}/estado', [CategoriaInventarioController::class, 'toggleEstado'])->name('categorias.estado');
    Route::resource('categorias', CategoriaInventarioController::class)->except(['create', 'edit']);

    // ── Inventario ────────────────────────────────────────────────
    Route::get('inventario/partials/specs/{tipo}', function ($tipo) {
        $tipos = ['computacional', 'mobiliario', 'vehiculo', 'herramienta', 'insumo'];
        if (! in_array($tipo, $tipos)) {
            abort(404);
        }

        return view("inventario.partials.specs.{$tipo}");
    })->name('inventario.specs');
    Route::get('inventario/{item}/perfil', [InventarioController::class, 'perfil'])->name('inventario.perfil');
    Route::patch('inventario/{item}/estado', [InventarioController::class, 'cambiarEstado'])->name('inventario.estado');
    Route::post('inventario/{item}/programas', [InventarioController::class, 'agregarPrograma'])->name('inventario.programas.agregar');
    Route::delete('inventario/{item}/programas/{programa}', [InventarioController::class, 'quitarPrograma'])->name('inventario.programas.quitar');
    Route::post('inventario/{item}/formateo', [InventarioController::class, 'registrarFormateo'])->name('inventario.formateo');
    Route::resource('inventario', InventarioController::class);

    // ── Asignaciones ──────────────────────────────────────────────
    Route::post('asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
    Route::patch('asignaciones/{asignacion}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');

    // ── Mermas ────────────────────────────────────────────────────
    Route::post('mermas', [MermaController::class, 'store'])->name('mermas.store');

    // ── Plantillas y carga masiva ─────────────────────────────────
    Route::get('plantillas/reporte/{archivo}', function($archivo) {
    $ruta = storage_path("app/temp_{$archivo}");
    if (!file_exists($ruta)) abort(404);
    return response()->download($ruta)->deleteFileAfterSend(true);
})->name('plantillas.reporte');

    Route::get('plantillas/{tipo}/descargar', [PlantillaController::class, 'descargar'])->name('plantillas.descargar');
    Route::post('plantillas/{tipo}/importar', [PlantillaController::class, 'importar'])->name('plantillas.importar');

    // ── Ubicaciones ───────────────────────────────────────────────
    Route::get('ubicaciones', [UbicacionController::class, 'index'])->name('ubicaciones.index');
    Route::get('ubicaciones/plano/{piso}', [UbicacionController::class, 'plano'])->name('ubicaciones.plano');

    // Edificios
    Route::post('edificios', [UbicacionController::class, 'storeEdificio'])->name('edificios.store');
    Route::put('edificios/{edificio}', [UbicacionController::class, 'updateEdificio'])->name('edificios.update');

    // Pisos
    Route::post('pisos', [UbicacionController::class, 'storePiso'])->name('pisos.store');
    Route::post('pisos/{piso}/update', [UbicacionController::class, 'updatePiso'])->name('pisos.update');
    Route::post('pisos/{piso}/limpiar', [UbicacionController::class, 'limpiarPiso'])->name('pisos.limpiar');
    Route::get('pisos/{piso}/historial', [UbicacionController::class, 'historialPiso'])->name('pisos.historial');
    Route::get('ubicaciones/piso/{piso}/puestos', [UbicacionController::class, 'puestosJson'])->name('ubicaciones.puestos');

    // Puestos
    Route::post('puestos', [UbicacionController::class, 'storePuesto'])->name('puestos.store');
    Route::put('puestos/{puesto}', [UbicacionController::class, 'updatePuesto'])->name('puestos.update');
    Route::delete('puestos/{puesto}', [UbicacionController::class, 'destroyPuesto'])->name('puestos.destroy');
    Route::patch('puestos/{puesto}/estado', [UbicacionController::class, 'toggleEstadoPuesto'])->name('puestos.estado');
    Route::post('puestos/{puesto}/asignar', [UbicacionController::class, 'asignar'])->name('puestos.asignar');
    Route::post('puestos/{puesto}/liberar', [UbicacionController::class, 'liberar'])->name('puestos.liberar');

    // Historial pisos
    Route::post('historial-pisos/{historialId}/rollback', [UbicacionController::class, 'rollbackPiso'])->name('pisos.rollback');

  Route::patch('proveedores/{proveedor}/estado', [ProveedorController::class, 'toggleEstado'])->name('proveedores.estado');
Route::resource('proveedores', ProveedorController::class)
    ->except(['create', 'edit'])
    ->parameters(['proveedores' => 'proveedor']);
    Route::post('notificaciones/leer-todas', function () {
        Notificacion::noLeidas()->update([
            'leido' => true,
            'leido_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    })->name('notificaciones.leerTodas');

    Route::post('notificaciones/{id}/leer', function ($id) {
        Notificacion::find($id)?->update([
            'leido' => true,
            'leido_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    })->name('notificaciones.leer');

    // Facturas
   Route::get('proveedores/{proveedor}/perfil', [FacturaController::class, 'index'])->name('proveedores.perfil');
    Route::post('facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::post('facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update');
    Route::delete('facturas/{factura}', [FacturaController::class, 'destroy'])->name('facturas.destroy');
    Route::post('facturas/{factura}/evaluar', [FacturaController::class, 'evaluar'])->name('facturas.evaluar');
    
    
  

// ── Auditoría ─────────────────────────────────────────────────
Route::get('auditoria/exportar', [AuditoriaController::class, 'exportar'])->name('auditoria.exportar');
Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
Route::get('auditoria/{id}', function($id) {
    $log = \Spatie\Activitylog\Models\Activity::with('causer')->findOrFail($id);
    return response()->json($log);
})->name('auditoria.show');


Route::post('asignaciones/masiva', [AsignacionController::class, 'storeMasiva'])->name('asignaciones.masiva.store');
Route::get('asignaciones/masiva/items', [AsignacionController::class, 'buscarItems'])->name('asignaciones.masiva.items');
Route::get('asignaciones/masiva/funcionario/{funcionario}', [AsignacionController::class, 'itemsFuncionario'])->name('asignaciones.masiva.funcionario');
   
Route::get('funcionarios/{funcionario}/items', [AsignacionController::class, 'itemsFuncionario'])->name('funcionarios.items');


// Insumos
Route::get('insumos/buscar',              [InsumoController::class, 'buscar'])->name('insumos.buscar');
Route::patch('insumos/{insumo}/estado',   [InsumoController::class, 'toggleEstado'])->name('insumos.estado');
Route::post('insumos/{insumo}/entrada',   [InsumoController::class, 'entrada'])->name('insumos.entrada');
Route::post('insumos/{insumo}/salida',    [InsumoController::class, 'salida'])->name('insumos.salida');
Route::resource('insumos', InsumoController::class)
    ->except(['create', 'edit'])
    ->parameters(['insumos' => 'insumo']);

Route::get('facturas/buscar', [FacturaController::class, 'buscar'])->name('facturas.buscar');


Route::get('funcionarios/{funcionario}/insumos', [InsumoController::class, 'movimientosFuncionario'])->name('funcionarios.insumos');


});
