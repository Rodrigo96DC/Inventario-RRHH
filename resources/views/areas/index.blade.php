@extends('layouts.app')

@section('title', 'Áreas')
@section('page-title', 'Áreas')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Áreas</h1>
        <p>Gestión de áreas del sistema</p>
    </div>
<div class="flex-center gap-8">
    @can('funcionarios.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nueva área
    </button>
    @endcan
</div>
</div>

<div class="card mb-16">
    <div class="flex-center gap-12">
        <input
            type="text"
            id="buscador"
            class="form-control"
            placeholder="Buscar área..."
            style="max-width:320px;"
            oninput="buscarDebounce()"
        >
    </div>
</div>

<div class="card">
    <div id="tablaContainer">
        @include('areas.partials.tabla', compact('areas'))
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('areas.partials.paginacion', compact('areas'))
    </div>
</div>

@include('areas.partials.modal')

<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de áreas</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:20px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Descarga la plantilla, rellénala con tus datos y súbela aquí.
            <a href="{{ route('plantillas.descargar', 'areas') }}"
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
            <button class="btn btn-primary" onclick="importarAreas()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/modules/areas.js') }}"></script>
@endpush