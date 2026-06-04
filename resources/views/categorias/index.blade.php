@extends('layouts.app')

@section('title', 'Categorías')
@section('page-title', 'Categorías de Inventario')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Categorías</h1>
        <p>Gestión de categorías del inventario</p>
    </div>
<div class="flex-center gap-8">
    @can('equipos.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nueva categoría
    </button>
    @endcan
</div>
</div>

<div class="card mb-16">
    <div class="flex-center gap-12">
        <input type="text" id="buscador" class="form-control"
            placeholder="Buscar categoría..." style="max-width:320px;"
            oninput="buscarDebounce()">
    </div>
</div>

<div class="card">
    <div id="tablaContainer">
        @include('categorias.partials.tabla', compact('categorias'))
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('categorias.partials.paginacion', compact('categorias'))
    </div>
</div>

@include('categorias.partials.modal')

<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de categorías</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:20px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Descarga la plantilla, rellénala con tus datos y súbela aquí.
            <a href="{{ route('plantillas.descargar', 'categorias') }}"
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
            <button class="btn btn-primary" onclick="importarCategorias()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/categorias.js') }}"></script>
@endpush