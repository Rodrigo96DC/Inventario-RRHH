@extends('layouts.app')

@section('title', $funcionario->nombre_completo)
@section('page-title', 'Perfil del Funcionario')

@section('content')

    {{-- Header del perfil --}}
    <div class="card mb-16">
        <div class="flex-center gap-16">
            <div
                style="width:64px;height:64px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:22px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                {{ $funcionario->iniciales }}
            </div>
            <div style="flex:1;">
                <div style="font-size:18px;font-weight:500;color:var(--text-primary);">
                    {{ $funcionario->nombre_completo }}
                </div>
                <div class="text-secondary text-sm" style="margin-top:2px;">
                    {{ $funcionario->cargo->nombre ?? '—' }} — {{ $funcionario->area->nombre ?? '—' }}
                </div>
                <div style="margin-top:8px;">
                    <span
                        class="pill {{ match ($funcionario->estado) {
                            'Activo' => 'pill-green',
                            'Inactivo' => 'pill-red',
                            'Licencia' => 'pill-amber',
                            'Vacaciones' => 'pill-blue',
                            default => 'pill-gray',
                        } }}">{{ $funcionario->estado }}</span>
                    <span class="pill pill-gray"
                        style="margin-left:6px;">{{ $funcionario->tipo_contrato ?? 'Sin contrato' }}</span>
                </div>
            </div>
            <div class="flex-center gap-8">
                @can('funcionarios.editar')
                    <button class="btn btn-secondary" onclick="editarFuncionario({{ $funcionario->id }})">
                        <i class="ti ti-edit"></i> Editar
                    </button>
                @endcan
                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs-wrapper">
        <div class="tabs-header">
            <button class="tab-btn active" onclick="cambiarTab('info', this)">
                <i class="ti ti-user"></i> Información
            </button>
            <button class="tab-btn" onclick="cambiarTab('laboral', this)">
                <i class="ti ti-briefcase"></i> Laboral
            </button>
            {{-- <button class="tab-btn" onclick="cambiarTab('historial', this)">
                <i class="ti ti-history"></i> Historial
            </button> --}}
            <button class="tab-btn" onclick="cambiarTab('documentos', this)">
                <i class="ti ti-file"></i> Documentos
            </button>
            <button class="tab-btn" onclick="cambiarTab('inventario', this)">
                <i class="ti ti-box"></i> Inventario
            </button>
            <button class="tab-btn" onclick="cambiarTab('insumos', this)">
    <i class="ti ti-package"></i> Insumos
</button>
        </div>

        {{-- Tab: Información personal --}}
        <div class="tab-content active" id="tab-info">
            <div class="grid-2">
                <div class="card">
                    <div class="card-title" style="margin-bottom:16px;">
                        <i class="ti ti-id-badge"></i> Datos personales
                    </div>
                    <table class="table-detail">
                        <tr>
                            <td class="detail-label">RUT</td>
                            <td>{{ $funcionario->rut }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Nombre completo</td>
                            <td>{{ $funcionario->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Email</td>
                            <td>{{ $funcionario->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Teléfono</td>
                            <td>{{ $funcionario->telefono ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Dirección</td>
                            <td>{{ $funcionario->direccion ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="card">
                    <div class="card-title" style="margin-bottom:16px;">
                        <i class="ti ti-map-pin"></i> Ubicación
                    </div>
                    <table class="table-detail">
                        <tr>
                            <td class="detail-label">Región</td>
                            <td>{{ $funcionario->region->nombre ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Provincia</td>
                            <td>{{ $funcionario->provincia->nombre ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Comuna</td>
                            <td>{{ $funcionario->comuna->nombre ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab: Información laboral --}}
        <div class="tab-content" id="tab-laboral">
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-briefcase"></i> Datos laborales
                </div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label">Área</td>
                        <td>{{ $funcionario->area->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Cargo</td>
                        <td>{{ $funcionario->cargo->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Grado</td>
                        <td>{{ $funcionario->cargo->grado ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Tipo contrato</td>
                        <td>{{ $funcionario->tipo_contrato ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Fecha ingreso</td>
                        <td>{{ $funcionario->fecha_ingreso?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Estado</td>
                        <td>
                            <span
                                class="pill {{ match ($funcionario->estado) {
                                    'Activo' => 'pill-green',
                                    'Inactivo' => 'pill-red',
                                    'Licencia' => 'pill-amber',
                                    'Vacaciones' => 'pill-blue',
                                    default => 'pill-gray',
                                } }}">{{ $funcionario->estado }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Trazabilidad --}}
            <div class="card" style="margin-top:16px;">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-shield-check"></i> Trazabilidad del registro
                </div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label">Creado por</td>
                        <td>{{ $funcionario->creadoPor->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Fecha creación</td>
                        <td>{{ $funcionario->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Última modificación por</td>
                        <td>{{ $funcionario->editadoPor->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Fecha modificación</td>
                        <td>{{ $funcionario->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

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
                            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">
                                {{ $log->description }}
                            </div>
                            <div class="text-secondary text-sm" style="margin-top:2px;">
                                Por {{ $log->causer->name ?? 'Sistema' }} — {{ $log->created_at->format('d/m/Y H:i') }}
                            </div>

                            @if ($log->properties->has('attributes') && $log->event === 'updated')
                                <div style="margin-top:8px;">
                                    @foreach ($log->properties['attributes'] as $campo => $valor)
                                        @if (isset($log->properties['old'][$campo]) && $log->properties['old'][$campo] != $valor)
                                            @php
                                                $camposOcultos = [
                                                    'updated_at',
                                                    'created_at',
                                                    'deleted_at',
                                                    'updated_by',
                                                    'created_by',
                                                    'deleted_by',
                                                ];
                                                $valorAnterior = $log->properties['old'][$campo];
                                                $valorNuevo = $valor;

                                                // Formatear fechas
                                                if (
                                                    str_contains((string) $valorAnterior, 'T') &&
                                                    str_contains((string) $valorAnterior, ':')
                                                ) {
                                                    try {
                                                        $valorAnterior = \Carbon\Carbon::parse($valorAnterior)->format(
                                                            'd/m/Y H:i',
                                                        );
                                                    } catch (\Exception $e) {
                                                    }
                                                }
                                                if (
                                                    str_contains((string) $valorNuevo, 'T') &&
                                                    str_contains((string) $valorNuevo, ':')
                                                ) {
                                                    try {
                                                        $valorNuevo = \Carbon\Carbon::parse($valorNuevo)->format(
                                                            'd/m/Y H:i',
                                                        );
                                                    } catch (\Exception $e) {
                                                    }
                                                }
                                            @endphp
                                            @if (!in_array($campo, $camposOcultos))
                                                <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                                    <strong>{{ $campo }}:</strong>
                                                    <span style="color:#991b1b;">{{ $valorAnterior ?? '—' }}</span>
                                                    → <span style="color:#065f46;">{{ $valorNuevo ?? '—' }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endif


                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:32px;color:var(--text-secondary);">
                        <i class="ti ti-history" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        Sin historial de cambios
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Tab: Documentos --}}
        <div class="tab-content" id="tab-documentos">
            <div class="card">
                <div style="text-align:center;padding:48px;color:var(--text-secondary);">
                    <i class="ti ti-file-off" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                    <div style="font-size:14px;font-weight:500;">Módulo de documentos pendiente</div>
                    <div class="text-sm" style="margin-top:4px;">Se implementará próximamente</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal editar (reutilizamos el mismo de index) --}}
    @include('funcionarios.partials.modal')

    {{-- Tab: Inventario --}}
<div class="tab-content" id="tab-inventario">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">
        <input type="hidden" id="funcPerfilId" value="{{ $funcionario->id }}">
        {{-- Panel izquierdo — ítems asignados --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-box"></i> Ítems asignados
                <span id="totalItemsAsignados" style="background:var(--primary);color:#fff;font-size:11px;padding:2px 8px;border-radius:20px;margin-left:6px;">0</span>
            </div>
            <div id="funcItemsAsignados" style="max-height:400px;overflow-y:auto;">
                <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
                    Cargando...
                </div>
            </div>
            <div class="form-group" style="margin-top:16px;">
                <label class="form-label">Fecha asignación <span class="required">*</span></label>
                <input type="date" id="funcMasivaFecha" class="form-control"
                    value="{{ date('Y-m-d') }}"
                    max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea id="funcMasivaObservacion" class="form-control" rows="2"></textarea>
            </div>
        </div>

        {{-- Panel derecho — buscar ítems --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-search"></i> Asignar ítems
            </div>

            <div class="form-group">
                <label class="form-label">Buscar ítem disponible</label>
                <input type="text" id="funcBuscarItem" class="form-control"
                    placeholder="Nombre, código, serial..."
                    oninput="buscarItemsFuncMasiva()">
            </div>

            <div id="funcListaItems"
                style="max-height:200px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;margin-bottom:16px;">
                <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
                    Busca un ítem para comenzar
                </div>
            </div>

            {{-- Seleccionados --}}
            <div id="funcSeleccionadosContainer" style="display:none;margin-bottom:16px;">
                <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">
                    ÍTEMS A ASIGNAR
                </div>
                <div id="funcListaSeleccionados"
                    style="max-height:180px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;">
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span id="funcContador" style="font-size:13px;color:var(--text-secondary);">
                    0 ítems seleccionados
                </span>
                <button class="btn btn-primary" onclick="ejecutarAsignacionFuncMasiva()">
                    <i class="ti ti-check"></i> Asignar seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tab: Insumos --}}
<div class="tab-content" id="tab-insumos">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

        {{-- Panel izquierdo — insumos despachados --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-package"></i> Insumos despachados
                <span id="totalInsumosDespachados" style="background:var(--primary);color:#fff;font-size:11px;padding:2px 8px;border-radius:20px;margin-left:6px;">0</span>
            </div>
            <div id="funcInsumosDespachados" style="max-height:400px;overflow-y:auto;">
                <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
                    Cargando...
                </div>
            </div>
        </div>

        {{-- Panel derecho — despachar insumos --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-search"></i> Despachar insumos
            </div>

            <div class="form-group">
                <label class="form-label">Buscar insumo disponible</label>
                <input type="text" id="funcBuscarInsumo" class="form-control"
                    placeholder="Nombre del insumo..."
                    oninput="buscarInsumoFunc()">
            </div>

            <div id="funcListaInsumos"
                style="max-height:200px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;margin-bottom:16px;">
                <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
                    Busca un insumo para comenzar
                </div>
            </div>

            {{-- Seleccionados --}}
            <div id="funcInsumosSeleccionadosContainer" style="display:none;margin-bottom:16px;">
                <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">
                    INSUMOS A DESPACHAR
                </div>
                <div id="funcInsumosSeleccionados"
                    style="max-height:180px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Fecha despacho <span class="required">*</span></label>
                <input type="date" id="funcInsumoFecha" class="form-control"
                    value="{{ date('Y-m-d') }}"
                    max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea id="funcInsumoObservacion" class="form-control" rows="2"></textarea>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span id="funcInsumoContador" style="font-size:13px;color:var(--text-secondary);">
                    0 insumos seleccionados
                </span>
                <button class="btn btn-primary" onclick="ejecutarDespachoInsumos()">
                    <i class="ti ti-check"></i> Despachar seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/modules/funcionarios.js') }}"></script>
    <script src="{{ asset('js/modules/funcionarios.perfil.js') }}"></script>
@endpush
