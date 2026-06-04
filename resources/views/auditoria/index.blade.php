@extends('layouts.app')

@section('title', 'Auditoría')
@section('page-title', 'Auditoría')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Auditoría</h1>
        <p>Registro completo de actividad del sistema</p>
    </div>
    @can('auditoria.ver')
    <button class="btn btn-secondary" onclick="openModal('modalExportar')">
        <i class="ti ti-file-spreadsheet"></i> Exportar Excel
    </button>
    @endcan
</div>

{{-- Filtros --}}
<div class="card mb-16">
    <div class="flex-center gap-12" style="flex-wrap:wrap;">
        <select id="filtroModelo" class="form-control" style="max-width:180px;" onchange="cargarTabla()">
            <option value="">Todos los modelos</option>
            @foreach($modelos as $modelo)
            <option value="{{ $modelo }}">{{ $modelo }}</option>
            @endforeach
        </select>
        <select id="filtroEvento" class="form-control" style="max-width:160px;" onchange="cargarTabla()">
            <option value="">Todos los eventos</option>
            <option value="created">Creado</option>
            <option value="updated">Actualizado</option>
            <option value="deleted">Eliminado</option>
        </select>
        <input type="text" id="filtroUsuario" class="form-control"
            placeholder="Buscar por usuario..."
            style="max-width:200px;"
            oninput="buscarDebounce()">
        <input type="date" id="filtroDesde" class="form-control"
            style="max-width:160px;" onchange="cargarTabla()">
        <input type="date" id="filtroHasta" class="form-control"
            style="max-width:160px;" onchange="cargarTabla()">
        <button class="btn btn-secondary btn-sm" onclick="limpiarFiltros()">
            <i class="ti ti-filter-off"></i> Limpiar
        </button>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div id="tablaContainer">
        @include('auditoria.partials.tabla', ['query' => $logs])
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('auditoria.partials.paginacion', ['query' => $logs])
    </div>
</div>

{{-- Modal detalle --}}
<div class="modal-overlay" id="modalDetalle">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <span class="modal-title">Detalle del registro</span>
            <button class="modal-close" onclick="closeModal('modalDetalle')">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div id="modalDetalleContenido"></div>
    </div>
</div>
<div class="modal-overlay" id="modalExportar">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title">Exportar Auditoría</span>
            <button class="modal-close" onclick="closeModal('modalExportar')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:16px;padding:14px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;">
            <i class="ti ti-alert-triangle" style="margin-right:6px;"></i>
            Por rendimiento, la exportación está limitada a <strong>máximo 3 meses</strong> por descarga y <strong>10.000 registros</strong>.
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Fecha desde <span class="required">*</span></label>
                <input type="date" id="exportDesde" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Fecha hasta <span class="required">*</span></label>
                <input type="date" id="exportHasta" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Modelo (opcional)</label>
            <select id="exportModelo" class="form-control">
                <option value="">Todos</option>
                @foreach($modelos as $modelo)
                <option value="{{ $modelo }}">{{ $modelo }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Evento (opcional)</label>
            <select id="exportEvento" class="form-control">
                <option value="">Todos</option>
                <option value="created">Creado</option>
                <option value="updated">Actualizado</option>
                <option value="deleted">Eliminado</option>
            </select>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalExportar')">Cancelar</button>
            <button class="btn btn-primary" onclick="exportarAuditoria()">
                <i class="ti ti-download"></i> Descargar Excel
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/auditoria.js') }}"></script>
@endpush