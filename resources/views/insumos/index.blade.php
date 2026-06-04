@extends('layouts.app')

@section('title', 'Insumos')
@section('page-title', 'Insumos')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Insumos</h1>
        <p>Gestión de insumos consumibles</p>
    </div>
<div class="flex-center gap-8">
    @can('equipos.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nuevo insumo
    </button>
    @endcan
</div>
</div>

{{-- Filtros --}}
<div class="card mb-16">
    <div class="flex-center gap-12" style="flex-wrap:wrap;">
        <input type="text" id="buscador" class="form-control"
            placeholder="Buscar por nombre..."
            style="max-width:280px;" oninput="buscarDebounce()">
        <select id="filtroEstado" class="form-control" style="max-width:160px;" onchange="cargarTabla()">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>
        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
            <input type="checkbox" id="filtroAlerta" onchange="cargarTabla()">
            <span>Solo con stock bajo</span>
        </label>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div id="tablaContainer">
        @include('insumos.partials.tabla', compact('insumos'))
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('insumos.partials.paginacion', compact('insumos'))
    </div>
</div>

@include('insumos.partials.modal')

<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de insumos</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:16px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Descarga la plantilla, rellénala y súbela. Los insumos duplicados serán omitidos.
            <a href="{{ route('plantillas.descargar', 'insumos') }}"
               style="display:block;margin-top:8px;font-weight:500;color:var(--primary);">
                <i class="ti ti-download"></i> Descargar plantilla Excel
            </a>
        </div>

        <div class="form-group">
            <label class="form-label">Archivo Excel <span class="required">*</span></label>
            <input type="file" id="archivoCargaMasivaInsumo" class="form-control" accept=".xlsx,.xls">
            <span class="form-hint">Solo archivos .xlsx o .xls, máximo 5MB</span>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCargaMasiva')">Cancelar</button>
            <button class="btn btn-primary" onclick="importarInsumos()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/insumos.js') }}"></script>
@endpush