@extends('layouts.app')

@section('title', 'Proveedores')
@section('page-title', 'Proveedores')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Proveedores</h1>
        <p>Gestión de proveedores del sistema</p>
    </div>
<div class="flex-center gap-8">
    @can('proveedores.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nuevo proveedor
    </button>
    @endcan
</div>
</div>

<div class="card mb-16">
    <div class="flex-center gap-12">
        <input type="text" id="buscador" class="form-control"
            placeholder="Buscar por razón social o RUT..."
            style="max-width:320px;" oninput="buscarDebounce()">
        <select id="filtroEstado" class="form-control" style="max-width:160px;" onchange="cargarTabla()">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>
    </div>
</div>

<div class="card">
    <div id="tablaContainer">
        @include('proveedores.partials.tabla', compact('proveedores'))
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('proveedores.partials.paginacion', compact('proveedores'))
    </div>
</div>

@include('proveedores.partials.modal')


<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de proveedores</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:16px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Descarga la plantilla, rellénala y súbela. Los RUT duplicados serán ignorados.
            <a href="{{ route('plantillas.descargar', 'proveedores') }}"
               style="display:block;margin-top:8px;font-weight:500;color:var(--primary);">
                <i class="ti ti-download"></i> Descargar plantilla Excel
            </a>
        </div>

        <div class="form-group">
            <label class="form-label">Archivo Excel <span class="required">*</span></label>
            <input type="file" id="archivoCargaMasiva" class="form-control" accept=".xlsx,.xls">
            <span class="form-hint">Solo archivos .xlsx o .xls, máximo 2MB</span>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCargaMasiva')">Cancelar</button>
            <button class="btn btn-primary" onclick="importarProveedores()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/proveedores.js') }}"></script>
@endpush