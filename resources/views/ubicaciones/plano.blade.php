@extends('layouts.app')

@section('title', $piso->nombre)
@section('page-title', 'Plano — ' . $piso->nombre)

@section('content')

<div class="flex-center gap-8">
    @can('funcionarios.crear')
    <button class="btn btn-secondary" id="btnModoEdicion" onclick="toggleModoEdicion()">
        <i class="ti ti-pencil"></i> Editar puestos
    </button>
  <button class="btn btn-secondary" onclick="abrirHistorial({{ $piso->id }})">
    <i class="ti ti-history"></i> Historial
</button>
    <button class="btn btn-danger btn-sm" onclick="openModal('modalLimpiarPiso')">
        <i class="ti ti-trash"></i> Limpiar piso
    </button>
    @endcan
    <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left"></i> Volver
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start;">

    {{-- Plano interactivo --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);display:flex;align-items:center;gap:12px;">
            <span style="font-size:13px;font-weight:500;color:var(--text-primary);">Plano</span>
            <div class="flex-center gap-8" style="margin-left:auto;font-size:12px;color:var(--text-secondary);">
                <span style="display:flex;align-items:center;gap:4px;">
                    <span style="width:12px;height:12px;border-radius:3px;background:#d1fae5;border:1px solid #6ee7b7;display:inline-block;"></span> Disponible
                </span>
                <span style="display:flex;align-items:center;gap:4px;">
                    <span style="width:12px;height:12px;border-radius:3px;background:#bfdbfe;border:1px solid #93c5fd;display:inline-block;"></span> Ocupado
                </span>
                <span style="display:flex;align-items:center;gap:4px;">
                    <span style="width:12px;height:12px;border-radius:3px;background:#f1f5f9;border:1px solid #cbd5e1;display:inline-block;"></span> Inhabilitado
                </span>
            </div>
            <div id="indicadorEdicion" style="display:none;font-size:12px;color:var(--primary);background:var(--primary-light);padding:3px 10px;border-radius:20px;">
                <i class="ti ti-pencil"></i> Modo edición activo — arrastra para crear puestos
            </div>
        </div>

        <div style="position:relative;overflow:auto;max-height:75vh;">
            @if($piso->imagen)
         <div style="position:relative;overflow:auto;max-height:75vh;">
    @if($piso->imagen)
        <div id="planoContainer" style="position:relative;display:inline-block;">
            <img id="planoImg"
                 src="{{ Storage::url($piso->imagen) }}"
                 style="display:block;max-width:100%;user-select:none;"
                 draggable="false">
            {{-- Canvas para dibujar encima --}}
            <canvas id="overlay"
                style="position:absolute;top:0;left:0;cursor:crosshair;display:none;">
            </canvas>
            {{-- SVG para mostrar puestos guardados --}}
            <svg id="planoSvg"
                 style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;">
            </svg>
        </div>
    @else
        <div style="padding:48px;text-align:center;color:var(--text-secondary);">
            <i class="ti ti-photo-off" style="font-size:40px;display:block;margin-bottom:8px;"></i>
            Sin plano cargado
        </div>
    @endif
</div>
            @else
                <div style="padding:48px;text-align:center;color:var(--text-secondary);">
                    <i class="ti ti-photo-off" style="font-size:40px;display:block;margin-bottom:8px;"></i>
                    Sin plano cargado
                </div>
            @endif
        </div>
    </div>

    {{-- Panel lateral --}}
    <div>
        {{-- Buscador de puestos --}}
        <div class="card mb-16">
            <input type="text" id="buscadorPuesto" class="form-control"
                placeholder="Buscar puesto o sección..."
                oninput="filtrarPuestos()">
        </div>

        {{-- Lista de puestos --}}
        <div class="card" style="padding:0;max-height:70vh;overflow-y:auto;">
            <div style="padding:12px 16px;border-bottom:0.5px solid var(--border);font-size:13px;font-weight:500;color:var(--text-primary);">
                Puestos — <span id="totalPuestos">0</span> registrados
            </div>
            <div id="listaPuestos" style="padding:8px 0;"></div>
        </div>
    </div>

</div>

{{-- Popup info puesto --}}
<div id="popupPuesto" style="display:none;position:fixed;z-index:500;background:var(--card-bg);border:0.5px solid var(--border);border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.15);padding:16px;min-width:240px;max-width:300px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <span id="popupTitulo" style="font-size:13px;font-weight:500;color:var(--text-primary);"></span>
        <button onclick="cerrarPopup()" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:16px;">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div id="popupContenido"></div>
</div>

{{-- Modal confirmar puesto --}}
<div class="modal-overlay" id="modalPuesto">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">Registrar Puesto</span>
            <button class="modal-close" onclick="closeModal('modalPuesto');cancelarDibujo();">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="puestoIdPiso" value="{{ $piso->id }}">
        <input type="hidden" id="puestoX1">
        <input type="hidden" id="puestoY1">
        <input type="hidden" id="puestoX2">
        <input type="hidden" id="puestoY2">
        <form id="formPuesto">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">N° Puesto <span class="required">*</span></label>
                    <input type="text" id="puestoNumero" class="form-control" placeholder="Ej: 48">
                </div>
                <div class="form-group">
                    <label class="form-label">Sección</label>
                    <input type="text" id="puestoSeccion" class="form-control" placeholder="Ej: 1304">
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalPuesto');cancelarDibujo();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarPuesto()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>

{{-- Modal asignar funcionario --}}
<div class="modal-overlay" id="modalAsignarPuesto">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title" id="modalAsignarTitulo">Asignar Funcionario</span>
            <button class="modal-close" onclick="closeModal('modalAsignarPuesto')">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="asignarPuestoId">
        <form id="formAsignarPuesto">
            <div class="form-group">
                <label class="form-label">Funcionario <span class="required">*</span></label>
                <input type="text" id="asignarBuscar" class="form-control"
                    placeholder="Buscar por nombre o RUT..."
                    oninput="buscarFuncionarioPuesto()">
                <div id="asignarResultados" style="display:none;border:0.5px solid var(--border);border-radius:8px;margin-top:4px;max-height:180px;overflow-y:auto;background:var(--card-bg);"></div>
                <input type="hidden" id="asignarFuncionarioId">
                <div id="asignarFuncionarioSeleccionado" style="display:none;margin-top:8px;padding:8px 12px;background:var(--primary-light);border-radius:6px;font-size:13px;color:var(--primary);"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha inicio <span class="required">*</span></label>
                <input type="date" id="asignarFecha" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea id="asignarObservacion" class="form-control" rows="2"></textarea>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalAsignarPuesto')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarAsignacionPuesto()">
                <i class="ti ti-device-floppy"></i> Asignar
            </button>
        </div>
    </div>
</div>
{{-- Modal limpiar piso --}}
<div class="modal-overlay" id="modalLimpiarPiso">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title" style="color:#991b1b;">
                <i class="ti ti-alert-triangle"></i> Limpiar piso
            </span>
            <button class="modal-close" onclick="closeModal('modalLimpiarPiso')">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div style="padding:14px;background:#fee2e2;border-radius:8px;margin-bottom:16px;font-size:13px;color:#991b1b;">
            <strong>⚠️ Atención:</strong> Esta acción eliminará todos los puestos del piso.
            Las asignaciones actuales quedarán guardadas en el historial y podrás hacer rollback si es necesario.
        </div>

        <div class="form-group">
            <label class="form-label">Motivo de la limpieza</label>
            <textarea id="limpiarMotivo" class="form-control" rows="2"
                placeholder="Ej: Reorganización del piso 13..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                Para confirmar escribe <strong>LIMPIAR PISO</strong>
                <span class="required">*</span>
            </label>
            <input type="text" id="limpiarFrase" class="form-control"
                placeholder="LIMPIAR PISO"
                oninput="verificarFrase()">
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalLimpiarPiso')">Cancelar</button>
            <button class="btn btn-danger" id="btnConfirmarLimpiar" disabled
                onclick="confirmarLimpiarPiso({{ $piso->id }})">
                <i class="ti ti-trash"></i> Confirmar limpieza
            </button>
        </div>
    </div>
</div>

{{-- Modal historial --}}
<div class="modal-overlay" id="modalHistorial">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ti ti-history"></i> Historial del piso
            </span>
            <button class="modal-close" onclick="closeModal('modalHistorial')">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div id="historialContenido" style="min-height:100px;">
            <div style="text-align:center;padding:24px;color:var(--text-secondary);">
                <i class="ti ti-loader" style="font-size:24px;"></i> Cargando...
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/ubicaciones.plano.js') }}"></script>
@endpush