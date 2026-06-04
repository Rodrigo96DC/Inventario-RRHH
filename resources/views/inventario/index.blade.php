@extends('layouts.app')

@section('title', 'Inventario')
@section('page-title', 'Inventario')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Inventario</h1>
        <p>Gestión de activos institucionales</p>
    </div>
<div class="flex-center gap-8">
    @can('equipos.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nuevo ítem
    </button>
    @endcan
</div>
</div>

{{-- Tabs --}}
<div class="tabs-wrapper">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="cambiarTab('inventario', this)">
            <i class="ti ti-box"></i> Inventario
        </button>
        @can('equipos.crear')
        <button class="tab-btn" onclick="cambiarTab('asignacion-masiva', this)">
            <i class="ti ti-users"></i> Asignación masiva
        </button>
        @endcan
    </div>

    {{-- Tab inventario --}}
    <div class="tab-content active" id="tab-inventario">
        {{-- Filtros --}}
        <div class="card mb-16">
            <div class="flex-center gap-12" style="flex-wrap:wrap;">
                <input type="text" id="buscador" class="form-control"
                    placeholder="Buscar por nombre, código, marca, serial..."
                    style="max-width:320px;" oninput="buscarDebounce()">
                <select id="filtroEstado" class="form-control" style="max-width:160px;" onchange="cargarTabla()">
                    <option value="">Todos los estados</option>
                    <option value="Disponible">Disponible</option>
                    <option value="Asignado">Asignado</option>
                    <option value="En mantención">En mantención</option>
                    <option value="De baja">De baja</option>
                    <option value="Extraviado">Extraviado</option>
                </select>
            </div>
        </div>

        <div class="card">
            <div id="tablaContainer">
                @include('inventario.partials.tabla', compact('items'))
            </div>
            <div id="paginacionContainer" style="margin-top:16px;">
                @include('inventario.partials.paginacion', compact('items'))
            </div>
        </div>
    </div>

    {{-- Tab asignación masiva --}}
    <div class="tab-content" id="tab-asignacion-masiva">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

            {{-- Panel izquierdo — Funcionario --}}
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-user"></i> Funcionario
                </div>

                <div class="form-group">
                    <label class="form-label">Buscar funcionario</label>
                    <input type="text" id="masivaBuscarFuncionario" class="form-control"
                        placeholder="Nombre o RUT..."
                        oninput="buscarFuncionarioMasiva()">
                    <div id="masivaResultadosFuncionario"
                        style="display:none;border:0.5px solid var(--border);border-radius:8px;margin-top:4px;max-height:200px;overflow-y:auto;background:var(--card-bg);">
                    </div>
                </div>

                <div id="masivaFuncionarioSeleccionado" style="display:none;">
                    <div style="padding:12px;background:var(--primary-light);border-radius:8px;margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div id="masivaFuncIniciales"
                                style="width:36px;height:36px;border-radius:50%;background:var(--primary);color:#fff;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            </div>
                            <div>
                                <div id="masivaFuncNombre" style="font-size:13px;font-weight:500;color:var(--text-primary);"></div>
                                <div id="masivaFuncCargo" style="font-size:12px;color:var(--text-secondary);"></div>
                            </div>
                            <button onclick="limpiarFuncionarioMasiva()"
                                style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--text-secondary);">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Items asignados al funcionario --}}
                    <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">
                        ÍTEMS ASIGNADOS
                    </div>
                    <div id="masivaItemsAsignados" style="max-height:300px;overflow-y:auto;">
                        <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
                            Sin ítems asignados
                        </div>
                    </div>
                </div>

                <input type="hidden" id="masivaFuncionarioId">

                <div class="form-group" style="margin-top:16px;">
                    <label class="form-label">Fecha asignación <span class="required">*</span></label>
                    <input type="date" id="masivaFecha" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Observación</label>
                    <textarea id="masivaObservacion" class="form-control" rows="2"></textarea>
                </div>
            </div>

       {{-- Panel derecho — Ítems --}}
<div class="card">
    <div class="card-title" style="margin-bottom:16px;">
        <i class="ti ti-box"></i> Seleccionar ítems
    </div>

    <div class="form-group">
        <label class="form-label">Buscar ítem</label>
        <input type="text" id="masivaBuscarItem" class="form-control"
            placeholder="Nombre, código, serial..."
            oninput="buscarItemsMasiva()">
    </div>

    <div id="masivaListaItems"
        style="max-height:200px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;margin-bottom:16px;">
        <div style="text-align:center;padding:20px;color:var(--text-secondary);font-size:13px;">
            Busca un ítem para comenzar
        </div>
    </div>

    {{-- Lista de seleccionados --}}
    <div id="masivaSeleccionadosContainer" style="display:none;margin-bottom:16px;">
        <div style="font-size:12px;font-weight:500;color:var(--text-secondary);margin-bottom:8px;">
            ÍTEMS A ASIGNAR
        </div>
        <div id="masivaListaSeleccionados"
            style="max-height:200px;overflow-y:auto;border:0.5px solid var(--border);border-radius:8px;">
        </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;">
        <span id="masivaContador" style="font-size:13px;color:var(--text-secondary);">
            0 ítems seleccionados
        </span>
        <button class="btn btn-primary" onclick="ejecutarAsignacionMasiva()">
            <i class="ti ti-check"></i> Asignar seleccionados
        </button>
    </div>
</div>

        </div>
    </div>
</div>

@include('inventario.partials.modal')


<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de inventario</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:16px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Selecciona el tipo de ítem, descarga la plantilla correspondiente y súbela con tus datos.
        </div>

        <div class="form-group">
            <label class="form-label">Tipo de ítem <span class="required">*</span></label>
            <select id="cargaTipo" class="form-control" onchange="actualizarLinkPlantilla()">
                <option value="">Seleccionar tipo...</option>
                <option value="inventario_computacional">Computacional</option>
                <option value="inventario_mobiliario">Mobiliario</option>
                <option value="inventario_vehiculo">Vehículo</option>
                <option value="inventario_herramienta">Herramienta</option>
            </select>
        </div>

        <div id="cargaPlantillaLink" style="display:none;margin-bottom:16px;">
            <a id="cargaPlantillaHref" href="#"
               style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--primary);font-weight:500;">
                <i class="ti ti-download"></i> Descargar plantilla
            </a>
        </div>

        <div class="form-group">
            <label class="form-label">Archivo Excel <span class="required">*</span></label>
            <input type="file" id="archivoCargaMasiva" class="form-control" accept=".xlsx,.xls">
            <span class="form-hint">Solo archivos .xlsx o .xls, máximo 5MB</span>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCargaMasiva')">Cancelar</button>
            <button class="btn btn-primary" onclick="importarInventario()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/inventario.js') }}"></script>
@endpush