@extends('layouts.app')

@section('title', 'Funcionarios')
@section('page-title', 'Funcionarios')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Funcionarios</h1>
        <p>Gestión del personal del sistema</p>
    </div>
    @can('funcionarios.crear')
  <div class="flex-center gap-8">
    @can('funcionarios.crear')
    <button class="btn btn-secondary" onclick="openModal('modalCargaMasiva')">
        <i class="ti ti-upload"></i> Carga masiva
    </button>
    <button class="btn btn-primary" onclick="abrirModalNuevo()">
        <i class="ti ti-plus"></i> Nuevo funcionario
    </button>
    @endcan
</div>
    @endcan
</div>

{{-- Filtros --}}
<div class="card mb-16">
    <div class="flex-center gap-12">
        <input
            type="text"
            id="buscador"
            class="form-control"
            placeholder="Buscar por nombre, apellido o RUT..."
            style="max-width:320px;"
            oninput="buscarDebounce()"
        >
        <select id="filtroEstado" class="form-control" style="max-width:160px;" onchange="cargarTabla()">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
            <option value="Licencia">Licencia</option>
            <option value="Vacaciones">Vacaciones</option>
        </select>
        <select id="filtroArea" class="form-control" style="max-width:200px;" onchange="cargarTabla()">
            <option value="">Todas las áreas</option>
            @foreach($areas as $area)
            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div id="tablaContainer">
        @include('funcionarios.partials.tabla', compact('funcionarios'))
    </div>
    <div id="paginacionContainer" style="margin-top:16px;">
        @include('funcionarios.partials.paginacion', compact('funcionarios'))
    </div>
</div>

{{-- Modal --}}
@include('funcionarios.partials.modal')

<div class="modal-overlay" id="modalCargaMasiva">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Carga masiva de funcionarios</span>
            <button class="modal-close" onclick="closeModal('modalCargaMasiva')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="margin-bottom:16px;padding:14px;background:var(--primary-light);border-radius:8px;font-size:13px;color:var(--primary);">
            <i class="ti ti-info-circle" style="margin-right:6px;"></i>
            Descarga la plantilla, rellénala y súbela. Los RUT duplicados serán ignorados.
            <a href="{{ route('plantillas.descargar', 'funcionarios') }}"
               style="display:block;margin-top:8px;font-weight:500;color:var(--primary);">
                <i class="ti ti-download"></i> Descargar plantilla Excel
            </a>
        </div>

        <div style="margin-bottom:16px;padding:12px;background:#fef3c7;border-radius:8px;font-size:12px;color:#92400e;">
            <strong>Campos opcionales:</strong> área y cargo deben coincidir exactamente con los registrados en el sistema.
            Tipo contrato: <strong>Planta, Contrata u Honorarios</strong>.
            Estado: <strong>Activo o Inactivo</strong>.
        </div>

        <div class="form-group">
            <label class="form-label">Archivo Excel <span class="required">*</span></label>
            <input type="file" id="archivoCargaMasiva" class="form-control" accept=".xlsx,.xls">
            <span class="form-hint">Solo archivos .xlsx o .xls, máximo 2MB</span>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalCargaMasiva')">Cancelar</button>
            <button class="btn btn-primary" onclick="importarFuncionarios()">
                <i class="ti ti-upload"></i> Importar
            </button>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/modules/funcionarios.js') }}"></script>
@endpush
