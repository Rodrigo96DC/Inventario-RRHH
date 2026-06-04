@extends('layouts.app')

@section('title', $insumo->nombre)
@section('page-title', 'Perfil del Insumo')

@section('content')

{{-- Header --}}
<div class="card mb-16">
    <div class="flex-center gap-16">
        <div style="width:56px;height:56px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ti ti-package" style="font-size:26px;"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:500;color:var(--text-primary);">
                {{ $insumo->nombre }}
            </div>
            <div class="text-secondary text-sm" style="margin-top:2px;">
                {{ $insumo->categoria->nombre ?? '—' }} — {{ $insumo->unidad_medida }}
                — Método: {{ $insumo->metodo_valuacion }}
            </div>
            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                <span class="pill {{ $insumo->color_stock }}">
                    Stock: {{ $insumo->stock_actual }}
                </span>
                @if($insumo->stock_bajo)
                <span class="pill pill-amber">
                    <i class="ti ti-alert-triangle"></i> Stock bajo (mín: {{ $insumo->stock_minimo }})
                </span>
                @endif
                <span class="pill {{ $insumo->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                    {{ $insumo->estado }}
                </span>
            </div>
        </div>
        <div class="flex-center gap-8">
            @can('equipos.crear')
            <button class="btn btn-primary btn-sm"
                onclick="abrirEntradaPerfil({{ $insumo->id }}, '{{ $insumo->nombre }}')">
                <i class="ti ti-package-import"></i> Registrar entrada
            </button>
            @endcan
            <a href="{{ route('insumos.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="tabs-wrapper">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="cambiarTab('info', this)">
            <i class="ti ti-info-circle"></i> Información
        </button>
        <button class="tab-btn" onclick="cambiarTab('lotes', this)">
            <i class="ti ti-layers"></i> Lotes
            <span style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                {{ $insumo->lotes->count() }}
            </span>
        </button>
        <button class="tab-btn" onclick="cambiarTab('movimientos', this)">
            <i class="ti ti-arrows-exchange"></i> Movimientos
            <span style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                {{ $insumo->movimientos->count() }}
            </span>
        </button>
        <button class="tab-btn" onclick="cambiarTab('historial', this)">
            <i class="ti ti-history"></i> Historial
        </button>
    </div>

    {{-- Tab info --}}
    <div class="tab-content active" id="tab-info">
        <div class="grid-2">
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-package"></i> Datos del insumo
                </div>
                <table class="table-detail">
                    <tr><td class="detail-label">Nombre</td><td>{{ $insumo->nombre }}</td></tr>
                    <tr><td class="detail-label">Descripción</td><td>{{ $insumo->descripcion ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Categoría</td><td>{{ $insumo->categoria->nombre ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Unidad medida</td><td>{{ $insumo->unidad_medida }}</td></tr>
                    <tr><td class="detail-label">Método valuación</td><td>{{ $insumo->metodo_valuacion }}</td></tr>
                    <tr><td class="detail-label">Stock mínimo</td><td>{{ $insumo->stock_minimo }}</td></tr>
                    <tr><td class="detail-label">Estado</td><td>
                        <span class="pill {{ $insumo->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                            {{ $insumo->estado }}
                        </span>
                    </td></tr>
                </table>
            </div>
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-chart-bar"></i> Resumen de stock
                </div>
                <table class="table-detail">
                    <tr><td class="detail-label">Stock actual</td><td>
                        <span class="pill {{ $insumo->color_stock }}">{{ $insumo->stock_actual }}</span>
                    </td></tr>
                    <tr><td class="detail-label">Stock mínimo</td><td>{{ $insumo->stock_minimo }}</td></tr>
                    <tr><td class="detail-label">Lotes activos</td><td>{{ $insumo->lotesActivos->count() }}</td></tr>
                    <tr><td class="detail-label">Total entradas</td><td>
                        {{ $insumo->movimientos->where('tipo', 'Entrada')->sum('cantidad') }}
                    </td></tr>
                    <tr><td class="detail-label">Total salidas</td><td>
                        {{ $insumo->movimientos->where('tipo', 'Salida')->sum('cantidad') }}
                    </td></tr>
                    <tr><td class="detail-label">Creado por</td><td>{{ $insumo->creadoPor->name ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Fecha creación</td><td>{{ $insumo->created_at->format('d/m/Y') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab lotes --}}
    <div class="tab-content" id="tab-lotes">
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-layers"></i> Lotes
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha ingreso</th>
                            <th>Cantidad inicial</th>
                            <th>Cantidad restante</th>
                            <th>Precio unitario</th>
                            <th>Fecha vencimiento</th>
                            <th>Factura</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($insumo->lotes->sortByDesc('fecha_ingreso') as $lote)
                        <tr>
                            <td>{{ $lote->fecha_ingreso->format('d/m/Y') }}</td>
                            <td>{{ $lote->cantidad_inicial }}</td>
                            <td>
                                <span class="pill {{ $lote->cantidad_restante > 0 ? 'pill-green' : 'pill-red' }}">
                                    {{ $lote->cantidad_restante }}
                                </span>
                            </td>
                            <td>{{ $lote->precio_unitario ? '$'.number_format($lote->precio_unitario, 0, ',', '.') : '—' }}</td>
                            <td>{{ $lote->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $lote->factura?->numero_factura ?? '—' }}</td>
                            <td>
                                <span class="pill {{ $lote->estado === 'Activo' ? 'pill-green' : 'pill-gray' }}">
                                    {{ $lote->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:24px;color:var(--text-secondary);">
                                Sin lotes registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab movimientos --}}
    <div class="tab-content" id="tab-movimientos">
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-arrows-exchange"></i> Movimientos
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Funcionario</th>
                            <th>Factura</th>
                            <th>Lote</th>
                            <th>Observación</th>
                            <th>Registrado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($insumo->movimientos->sortByDesc('fecha') as $mov)
                        <tr>
                            <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                            <td>
                                <span class="pill {{ $mov->tipo === 'Entrada' ? 'pill-green' : 'pill-blue' }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td>{{ $mov->cantidad }}</td>
                            <td>{{ $mov->funcionario?->nombre_completo ?? '—' }}</td>
                            <td>{{ $mov->factura?->numero_factura ?? '—' }}</td>
                            <td>{{ $mov->lote?->fecha_ingreso?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $mov->observacion ?? '—' }}</td>
                            <td>{{ $mov->creadoPor?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:24px;color:var(--text-secondary);">
                                Sin movimientos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab historial --}}
    <div class="tab-content" id="tab-historial">
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-history"></i> Historial de cambios
            </div>
            @forelse($actividad as $log)
            <div style="padding:12px 0;border-bottom:0.5px solid var(--border);display:flex;gap:12px;align-items:flex-start;">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti ti-{{ match($log->event) {
                        'created' => 'plus',
                        'updated' => 'edit',
                        'deleted' => 'trash',
                        default   => 'info-circle',
                    } }}"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:500;color:var(--text-primary);">{{ $log->description }}</div>
                    <div class="text-secondary text-sm" style="margin-top:2px;">
                        Por {{ $log->causer->name ?? 'Sistema' }} — {{ $log->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:var(--text-secondary);">
                Sin historial de cambios
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal entrada desde perfil --}}
@include('insumos.partials.modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/modules/insumos.js') }}"></script>
    <script>
        function abrirEntradaPerfil(id, nombre) {
            abrirEntrada(id, nombre);
        }
    </script>
@endpush