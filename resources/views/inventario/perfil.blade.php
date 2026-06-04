@extends('layouts.app')

@section('title', $item->nombre)
@section('page-title', 'Perfil del Ítem')

@section('content')

    {{-- Header --}}
    <div class="card mb-16">
        <div class="flex-center gap-16">
            @if ($item->imagen)
                <img src="{{ Storage::url($item->imagen) }}"
                    style="width:72px;height:72px;border-radius:10px;object-fit:cover;flex-shrink:0;">
            @else
                <div
                    style="width:72px;height:72px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti {{ $item->categoria->icono ?? 'ti-box' }}" style="font-size:32px;"></i>
                </div>
            @endif
            <div style="flex:1;">
                <div style="font-size:18px;font-weight:500;color:var(--text-primary);">
                    {{ $item->nombre }}
                </div>
                <div class="text-secondary text-sm" style="margin-top:2px;">
                    {{ $item->categoria->nombre }} — {{ $item->marca ?? '—' }} {{ $item->modelo ?? '' }}
                </div>
                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="pill {{ $item->color_estado }}">{{ $item->estado }}</span>
                    @if ($item->codigo_interno)
                        <span class="pill pill-gray">{{ $item->codigo_interno }}</span>
                    @endif
                    @if ($item->asignacionActiva)
                        <span class="pill pill-blue">
                            <i class="ti ti-user" style="font-size:11px;margin-right:3px;"></i>
                            {{ $item->asignacionActiva->funcionario->nombre_completo }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex-center gap-8">
                @can('equipos.editar')
                    <button class="btn btn-secondary" onclick="editarItem({{ $item->id }})">
                        <i class="ti ti-edit"></i> Editar
                    </button>
                @endcan
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
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
            <button class="tab-btn" onclick="cambiarTab('specs', this)">
                <i class="ti ti-settings"></i> Especificaciones
            </button>
            <button class="tab-btn" onclick="cambiarTab('asignaciones', this)">
                <i class="ti ti-users"></i> Asignaciones
                <span
                    style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                    {{ $item->asignaciones->count() }}
                </span>
            </button>
            @if ($item->categoria->nombre === 'Insumo')
                <button class="tab-btn" onclick="cambiarTab('mermas', this)">
                    <i class="ti ti-trending-down"></i> Mermas
                    <span
                        style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                        {{ $item->mermas->count() }}
                    </span>
                </button>
            @endif
            @if ($item->categoria->nombre === 'Computacional')
                <button class="tab-btn" onclick="cambiarTab('programas', this)">
                    <i class="ti ti-apps"></i> Programas
                    <span
                        style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                        {{ $item->programas->count() }}
                    </span>
                </button>
            @endif
            <button class="tab-btn" onclick="cambiarTab('historial', this)">
                <i class="ti ti-history"></i> Historial
            </button>
        </div>

        {{-- Tab: Información --}}
        <div class="tab-content active" id="tab-info">
            <div class="grid-2">
                <div class="card">
                    <div class="card-title" style="margin-bottom:16px;">
                        <i class="ti ti-box"></i> Datos generales
                    </div>
                    <table class="table-detail">
                        <tr>
                            <td class="detail-label">Nombre</td>
                            <td>{{ $item->nombre }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Categoría</td>
                            <td>{{ $item->categoria->nombre }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Marca</td>
                            <td>{{ $item->marca ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Modelo</td>
                            <td>{{ $item->modelo ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Código interno</td>
                            <td>{{ $item->codigo_interno ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">N° Serie</td>
                            <td>{{ $item->numero_serie ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Precio</td>
                            <td>{{ $item->precio ? '$' . number_format($item->precio, 0, ',', '.') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Fecha adquisición</td>
                            <td>{{ $item->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Ubicación</td>
                            <td>{{ $item->ubicacion ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Estado</td>
                            <td><span class="pill {{ $item->color_estado }}">{{ $item->estado }}</span></td>
                        </tr>
                        <tr>
                            <td class="detail-label">Observación</td>
                            <td>{{ $item->observacion ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <div class="card" style="margin-bottom:16px;">
                        <div class="card-title" style="margin-bottom:16px;">
                            <i class="ti ti-truck"></i> Proveedor y factura
                        </div>
                        <table class="table-detail">
                            <tr>
                                <td class="detail-label">Proveedor</td>
                                <td>{{ $item->proveedor->razon_social ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">RUT proveedor</td>
                                <td>{{ $item->proveedor->rut ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">N° Factura</td>
                                <td>{{ $item->factura->numero_factura ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Fecha factura</td>
                                <td>{{ $item->factura?->fecha_factura?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Monto factura</td>
                                <td>{{ $item->factura?->monto_total ? '$' . number_format($item->factura->monto_total, 0, ',', '.') : '—' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card">
                        <div class="card-title" style="margin-bottom:16px;">
                            <i class="ti ti-shield-check"></i> Trazabilidad
                        </div>
                        <table class="table-detail">
                            <tr>
                                <td class="detail-label">Creado por</td>
                                <td>{{ $creadoPor->name ?? '—' }}</td>
                            </tr>
                          
                          
                            <tr>
                                <td class="detail-label">Fecha creación</td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                           <tr>
                                <td class="detail-label">Modificado por</td>
                                <td>{{ $editadoPor->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Última modificación</td>
                                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Specs --}}
        <div class="tab-content" id="tab-specs">
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti {{ $item->categoria->icono ?? 'ti-box' }}"></i>
                    Especificaciones — {{ $item->categoria->nombre }}
                </div>
                @php $specs = $item->getSpecs(); @endphp
                @if ($specs)
                    @switch($item->categoria->nombre)
                        @case('Computacional')
                            <table class="table-detail">
                                <tr>
                                    <td class="detail-label">N° Inventario</td>
                                    <td>{{ $specs->numero_inventario ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Procesador</td>
                                    <td>{{ $specs->procesador ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">RAM</td>
                                    <td>{{ $specs->ram ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Tipo disco</td>
                                    <td>{{ $specs->tipo_disco ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Capacidad disco</td>
                                    <td>{{ $specs->capacidad_disco ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">MAC</td>
                                    <td>{{ $specs->mac ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">IP</td>
                                    <td>{{ $specs->ip ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Hostname</td>
                                    <td>{{ $specs->hostname ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Entradas</td>
                                    <td>{{ $specs->entradas ?? '—' }}</td>
                                </tr>
                            </table>
                        @break

                        @case('Mobiliario')
                            <table class="table-detail">
                                <tr>
                                    <td class="detail-label">Material</td>
                                    <td>{{ $specs->material ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Color</td>
                                    <td>{{ $specs->color ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Dimensiones</td>
                                    <td>{{ $specs->dimensiones ?? '—' }}</td>
                                </tr>
                            </table>
                        @break

                        @case('Vehículo')
                            <table class="table-detail">
                                <tr>
                                    <td class="detail-label">Patente</td>
                                    <td>{{ $specs->patente ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Año</td>
                                    <td>{{ $specs->anio ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Tipo combustible</td>
                                    <td>{{ $specs->tipo_combustible ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Kilometraje</td>
                                    <td>{{ $specs->kilometraje ? number_format($specs->kilometraje, 0, ',', '.') . ' km' : '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="detail-label">N° Motor</td>
                                    <td>{{ $specs->numero_motor ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">N° Chasis</td>
                                    <td>{{ $specs->numero_chasis ?? '—' }}</td>
                                </tr>
                            </table>
                        @break

                        @case('Herramienta')
                            <table class="table-detail">
                                <tr>
                                    <td class="detail-label">Tipo</td>
                                    <td>{{ $specs->tipo ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Material</td>
                                    <td>{{ $specs->material ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Medida</td>
                                    <td>{{ $specs->medida ?? '—' }}</td>
                                </tr>
                            </table>
                        @break

                        @case('Insumo')
                            <table class="table-detail">
                                <tr>
                                    <td class="detail-label">Stock inicial</td>
                                    <td>{{ $specs->stock_inicial }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Stock actual</td>
                                    <td>
                                        <span
                                            class="pill {{ $specs->stock_actual <= $specs->stock_minimo ? 'pill-red' : 'pill-green' }}">
                                            {{ $specs->stock_actual }} {{ $specs->unidad_medida }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Stock mínimo</td>
                                    <td>{{ $specs->stock_minimo }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label">Unidad medida</td>
                                    <td>{{ $specs->unidad_medida ?? '—' }}</td>
                                </tr>
                            </table>
                        @break
                    @endswitch
                @else
                    <div style="text-align:center;padding:32px;color:var(--text-secondary);">
                        <i class="ti ti-info-circle" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        Sin especificaciones registradas
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Asignaciones --}}
        <div class="tab-content" id="tab-asignaciones">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ti ti-users"></i> Historial de asignaciones
                    </div>
                    @can('equipos.editar')
                        <button class="btn btn-primary btn-sm" onclick="openModal('modalAsignacion')">
                            <i class="ti ti-plus"></i> Nueva asignación
                        </button>
                    @endcan
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Funcionario</th>
                                <th>Cantidad</th>
                                <th>Fecha asignación</th>
                                <th>Fecha devolución</th>
                                <th>Estado</th>
                                <th>Asignado por</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($item->asignaciones as $asig)
                                <tr>
                                    <td>
                                        <a href="{{ route('funcionarios.perfil', $asig->funcionario->id) }}"
                                            style="color:var(--primary);">
                                            {{ $asig->funcionario->nombre_completo }}
                                        </a>
                                    </td>
                                    <td>{{ $asig->cantidad }}</td>
                                    <td>{{ $asig->fecha_asignacion->format('d/m/Y') }}</td>
                                    <td>{{ $asig->fecha_devolucion?->format('d/m/Y') ?? '—' }}</td>
                                    <td>
                                        <span class="pill {{ $asig->estado === 'Activo' ? 'pill-green' : 'pill-gray' }}">
                                            {{ $asig->estado }}
                                        </span>
                                    </td>
                                    <td class="text-secondary text-sm">{{ $asig->creadoPor->name ?? '—' }}</td>
                                    <td>
                                        @if ($asig->estado === 'Activo')
                                            @can('equipos.editar')
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="devolverAsignacion({{ $asig->id }})">
                                                    <i class="ti ti-arrow-back-up"></i> Devolver
                                                </button>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        style="text-align:center;padding:24px;color:var(--text-secondary);">
                                        Sin asignaciones registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab: Mermas --}}
        @if ($item->categoria->nombre === 'Insumo')
            <div class="tab-content" id="tab-mermas">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ti ti-trending-down"></i> Registro de mermas
                        </div>
                        @can('equipos.editar')
                            <button class="btn btn-primary btn-sm" onclick="openModal('modalMerma')">
                                <i class="ti ti-plus"></i> Registrar merma
                            </button>
                        @endcan
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                    <th>Descripción</th>
                                    <th>Lugar guardado</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->mermas as $merma)
                                    <tr>
                                        <td>{{ $merma->fecha_merma->format('d/m/Y') }}</td>
                                        <td>{{ $merma->cantidad }}</td>
                                        <td>{{ $merma->descripcion }}</td>
                                        <td>{{ $merma->lugar_guardado ?? '—' }}</td>
                                        <td class="text-secondary text-sm">{{ $merma->creadoPor->name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            style="text-align:center;padding:24px;color:var(--text-secondary);">
                                            Sin mermas registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tab: Programas --}}
        @if ($item->categoria->nombre === 'Computacional')
            <div class="tab-content" id="tab-programas">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ti ti-apps"></i> Programas instalados
                        </div>
                        @can('equipos.editar')
                            <div class="flex-center gap-8">
                                <button class="btn btn-secondary btn-sm" onclick="openModal('modalFormateo')">
                                    <i class="ti ti-refresh"></i> Registrar formateo
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="openModal('modalPrograma')">
                                    <i class="ti ti-plus"></i> Agregar programa
                                </button>
                            </div>
                        @endcan
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Grupo</th>
                                    <th>Versión</th>
                                    <th>Fecha instalación</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->programas as $prog)
                                    <tr>
                                        <td style="font-weight:500;">{{ $prog->nombre }}</td>
                                        <td>{{ $prog->grupo ?? '—' }}</td>
                                        <td>{{ $prog->version ?? '—' }}</td>
                                        <td>{{ $prog->pivot->fecha_instalacion ? \Carbon\Carbon::parse($prog->pivot->fecha_instalacion)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td>
                                            @can('equipos.editar')
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="quitarPrograma({{ $item->id }}, {{ $prog->id }})">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            style="text-align:center;padding:24px;color:var(--text-secondary);">
                                            Sin programas instalados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Historial de formateos --}}
                @if ($item->formatos->count() > 0)
                    <div class="card" style="margin-top:16px;">
                        <div class="card-title" style="margin-bottom:16px;">
                            <i class="ti ti-history"></i> Historial de formateos
                        </div>
                        @foreach ($item->formatos as $formato)
                            <div style="padding:12px 0;border-bottom:0.5px solid var(--border);">
                                <div class="flex-center gap-8">
                                    <span
                                        style="font-size:13px;font-weight:500;">{{ $formato->fecha_formato->format('d/m/Y') }}</span>
                                    <span class="text-secondary text-sm">por {{ $formato->creadoPor->name ?? '—' }}</span>
                                </div>
                                @if ($formato->observacion)
                                    <div class="text-secondary text-sm" style="margin-top:4px;">
                                        {{ $formato->observacion }}</div>
                                @endif
                                @if ($formato->programas_anteriores)
                                    <div style="margin-top:6px;font-size:12px;color:var(--text-secondary);">
                                        Programas anteriores: {{ implode(', ', $formato->programas_anteriores) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Tab: Historial --}}
        <div class="tab-content" id="tab-historial">
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-history"></i> Historial de cambios
                </div>
                @forelse($actividad as $log)
                    <div
                        style="padding:12px 0;border-bottom:0.5px solid var(--border);display:flex;gap:12px;align-items:flex-start;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i
                                class="ti ti-{{ match ($log->event) {
                                    'created' => 'plus',
                                    'updated' => 'edit',
                                    'deleted' => 'trash',
                                    default => 'info-circle',
                                } }}"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">{{ $log->description }}
                            </div>
                            <div class="text-secondary text-sm" style="margin-top:2px;">
                                Por {{ $log->causer->name ?? 'Sistema' }} — {{ $log->created_at->format('d/m/Y H:i') }}
                            </div>
                            @if ($log->properties->has('attributes') && $log->event === 'updated')
                                <div style="margin-top:8px;">
                                    @foreach ($log->properties['attributes'] as $campo => $valor)
                                        @if (isset($log->properties['old'][$campo]) && $log->properties['old'][$campo] != $valor)
                                            <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                                <strong>{{ $campo }}:</strong>
                                                <span
                                                    style="color:#991b1b;">{{ $log->properties['old'][$campo] ?? '—' }}</span>
                                                → <span style="color:#065f46;">{{ $valor ?? '—' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
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

    {{-- Modal asignación --}}
    <div class="modal-overlay" id="modalAsignacion">
        <div class="modal" style="max-width:500px;">
            <div class="modal-header">
                <span class="modal-title">Nueva Asignación</span>
                <button class="modal-close" onclick="closeModal('modalAsignacion')">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="formAsignacion">
                <div class="form-group">
                    <label class="form-label">Funcionario <span class="required">*</span></label>
                    <input type="text" id="asigBuscar" class="form-control" placeholder="Buscar por nombre o RUT..."
                        oninput="buscarFuncionarioAsig()">
                    <div id="asigResultados"
                        style="display:none;border:0.5px solid var(--border);border-radius:8px;margin-top:4px;max-height:200px;overflow-y:auto;background:var(--card-bg);">
                    </div>
                    <input type="hidden" id="asigFuncionarioId">
                    <div id="asigFuncionarioSeleccionado"
                        style="display:none;margin-top:8px;padding:8px 12px;background:var(--primary-light);border-radius:6px;font-size:13px;color:var(--primary);">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Cantidad <span class="required">*</span></label>
                        <input type="number" id="asigCantidad" class="form-control" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha asignación <span class="required">*</span></label>
                        <input type="date" id="asigFecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Observación</label>
                    <textarea id="asigObservacion" class="form-control" rows="2"></textarea>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modalAsignacion')">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarAsignacion({{ $item->id }})">
                    <i class="ti ti-device-floppy"></i> Guardar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal merma --}}
    <div class="modal-overlay" id="modalMerma">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <span class="modal-title">Registrar Merma</span>
                <button class="modal-close" onclick="closeModal('modalMerma')">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="formMerma">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Cantidad <span class="required">*</span></label>
                        <input type="number" id="mermaCantidad" class="form-control" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha <span class="required">*</span></label>
                        <input type="date" id="mermaFecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción <span class="required">*</span></label>
                    <textarea id="mermaDescripcion" class="form-control" rows="2" placeholder="Motivo de la merma..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Lugar de guardado</label>
                    <input type="text" id="mermaLugar" class="form-control" placeholder="Dónde quedó guardado...">
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modalMerma')">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarMerma({{ $item->id }})">
                    <i class="ti ti-device-floppy"></i> Registrar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal programa --}}
    @if ($item->categoria->nombre === 'Computacional')
        <div class="modal-overlay" id="modalPrograma">
            <div class="modal" style="max-width:480px;">
                <div class="modal-header">
                    <span class="modal-title">Agregar Programa</span>
                    <button class="modal-close" onclick="closeModal('modalPrograma')">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="formPrograma">
                    <div class="form-group">
                        <label class="form-label">Programa <span class="required">*</span></label>
                        <select id="progId" class="form-control">
                            <option value="">Seleccionar programa</option>
                            @foreach (\App\Models\Programa::where('estado', 'Instalar')->orderBy('nombre')->get() as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->nombre }}
                                    {{ $prog->version ? 'v' . $prog->version : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha instalación</label>
                        <input type="date" id="progFecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal('modalPrograma')">Cancelar</button>
                    <button class="btn btn-primary" onclick="agregarPrograma({{ $item->id }})">
                        <i class="ti ti-device-floppy"></i> Agregar
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal formateo --}}
        <div class="modal-overlay" id="modalFormateo">
            <div class="modal" style="max-width:480px;">
                <div class="modal-header">
                    <span class="modal-title">Registrar Formateo</span>
                    <button class="modal-close" onclick="closeModal('modalFormateo')">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="formFormateo">
                    <div class="form-group">
                        <label class="form-label">Fecha formateo <span class="required">*</span></label>
                        <input type="date" id="formateoFecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observación</label>
                        <textarea id="formateoObservacion" class="form-control" rows="2" placeholder="Motivo del formateo..."></textarea>
                    </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal('modalFormateo')">Cancelar</button>
                    <button class="btn btn-primary" onclick="registrarFormateo({{ $item->id }})">
                        <i class="ti ti-refresh"></i> Formatear
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal editar item --}}
    @include('inventario.partials.modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/modules/inventario.js') }}"></script>
    <script src="{{ asset('js/modules/inventario.perfil.js') }}"></script>
@endpush
