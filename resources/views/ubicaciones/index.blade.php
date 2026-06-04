@extends('layouts.app')

@section('title', 'Ubicaciones')
@section('page-title', 'Ubicaciones')

@section('content')

    <div class="page-header">
        <div class="page-header-info">
            <h1>Ubicaciones</h1>
            <p>Gestión de edificios, pisos y puestos</p>
        </div>
        @can('funcionarios.crear')
            <button class="btn btn-primary" onclick="openModal('modalEdificio')">
                <i class="ti ti-plus"></i> Nuevo edificio
            </button>
        @endcan
    </div>

    {{-- Edificios --}}
    @forelse($edificios as $edificio)
        <div class="card mb-16">
            <div class="card-header">
                <div class="card-title">
                    <i class="ti ti-building"></i>
                    {{ $edificio->nombre }}
                    <span class="text-secondary text-sm" style="font-weight:400;margin-left:6px;">
                        {{ $edificio->direccion ?? '' }}
                    </span>
                    <span class="pill {{ $edificio->estado === 'Activo' ? 'pill-green' : 'pill-red' }}"
                        style="margin-left:8px;">
                        {{ $edificio->estado }}
                    </span>
                </div>
                <div class="flex-center gap-8">
                    @can('funcionarios.crear')
                        <button class="btn btn-secondary btn-sm" onclick="abrirModalPiso({{ $edificio->id }})">
                            <i class="ti ti-plus"></i> Agregar piso
                        </button>
                        <button class="btn btn-secondary btn-sm"
                            onclick="editarEdificio({{ $edificio->id }}, '{{ $edificio->nombre }}', '{{ $edificio->direccion }}', '{{ $edificio->estado }}')">
                            <i class="ti ti-edit"></i>
                        </button>
                    @endcan
                </div>
            </div>

            {{-- Pisos del edificio --}}
            @if ($edificio->pisos->count() > 0)
                <div
                    style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-top:16px;">
                    @foreach ($edificio->pisos->sortBy('numero') as $piso)
                        <a href="{{ route('ubicaciones.plano', $piso->id) }}"
                            style="display:block;border:0.5px solid var(--border);border-radius:8px;overflow:hidden;transition:all 0.15s;text-decoration:none;position:relative;{{ $piso->estado === 'Inactivo' ? 'opacity:0.6;' : '' }}"
                            onmouseover="this.style.borderColor='var(--primary)'"
                            onmouseout="this.style.borderColor='var(--border)'">

                            @if ($piso->imagen)
                                <img src="{{ Storage::url($piso->imagen) }}"
                                    style="width:100%;height:120px;object-fit:cover;">
                            @else
                                <div
                                    style="width:100%;height:120px;background:var(--hover-bg);display:flex;align-items:center;justify-content:center;">
                                    <i class="ti ti-building" style="font-size:40px;color:var(--text-muted);"></i>
                                </div>
                            @endif

                            {{-- Badge estado --}}
                            @if ($piso->estado === 'Inactivo')
                                <div
                                    style="position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;">
                                    Inactivo
                                </div>
                            @endif

                            <div style="padding:10px 12px;">
                                <div style="font-size:13px;font-weight:500;color:var(--text-primary);">
                                    {{ $piso->nombre }}
                                </div>
                                <div class="text-secondary text-sm">
                                    Piso {{ $piso->numero }} —
                                    {{ $piso->puestos()->count() }} puestos
                                    @if ($piso->estado === 'Inactivo')
                                        <span style="color:#ef4444;"> — Inactivo</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Botón editar --}}
                            @can('funcionarios.crear')
                                <button
                                    onclick="event.preventDefault();event.stopPropagation();editarPiso({{ $piso->id }}, '{{ $piso->nombre }}', {{ $piso->numero }}, '{{ $piso->estado }}')"
                                    style="position:absolute;bottom:8px;right:8px;width:28px;height:28px;border-radius:6px;background:var(--card-bg);border:0.5px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-secondary);"
                                    onmouseover="this.style.background='var(--primary-light)';this.style.color='var(--primary)'"
                                    onmouseout="this.style.background='var(--card-bg)';this.style.color='var(--text-secondary)'"
                                    title="Editar piso">
                                    <i class="ti ti-edit" style="font-size:14px;"></i>
                                </button>
                            @endcan
                        </a>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:24px;color:var(--text-secondary);font-size:13px;">
                    Sin pisos registrados — agrega uno con el botón de arriba
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center;padding:48px;color:var(--text-secondary);">
            <i class="ti ti-building-off" style="font-size:40px;display:block;margin-bottom:12px;"></i>
            <div style="font-size:14px;font-weight:500;">Sin edificios registrados</div>
            <div class="text-sm" style="margin-top:4px;">Crea un edificio para comenzar</div>
        </div>
    @endforelse

    {{-- Modal edificio --}}
    <div class="modal-overlay" id="modalEdificio">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title" id="modalEdificioTitulo">Nuevo Edificio</span>
                <button class="modal-close" onclick="closeModal('modalEdificio')">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <input type="hidden" id="edificioId">
            <form id="formEdificio">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" id="edNombre" class="form-control" placeholder="Ej: Edificio Central">
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" id="edDireccion" class="form-control" placeholder="Dirección">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select id="edEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modalEdificio')">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarEdificio()">
                    <i class="ti ti-device-floppy"></i> Guardar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal piso --}}
    <div class="modal-overlay" id="modalPiso">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title">Nuevo Piso</span>
                <button class="modal-close" onclick="closeModal('modalPiso')">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <input type="hidden" id="pisoEdificioId">
            <form id="formPiso" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" id="pisoNombre" class="form-control" placeholder="Ej: Piso 13">
                </div>
                <div class="form-group">
                    <label class="form-label">Número <span class="required">*</span></label>
                    <input type="number" id="pisoNumero" class="form-control" placeholder="13">
                </div>
                <div class="form-group">
                    <label class="form-label">Plano del piso <span class="text-secondary text-sm">(imagen, máx
                            5MB)</span></label>
                    <input type="file" id="pisoImagen" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select id="pisoEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modalPiso')">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarPiso()">
                    <i class="ti ti-device-floppy"></i> Guardar
                </button>
            </div>
        </div>
    </div>
    {{-- Modal editar piso --}}
    <div class="modal-overlay" id="modalEditarPiso">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title">Editar Piso</span>
                <button class="modal-close" onclick="closeModal('modalEditarPiso')">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <input type="hidden" id="editarPisoId">
            <form id="formEditarPiso" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" id="editarPisoNombre" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Número <span class="required">*</span></label>
                    <input type="number" id="editarPisoNumero" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Reemplazar plano <span
                            class="text-secondary text-sm">(opcional)</span></label>
                    <input type="file" id="editarPisoImagen" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select id="editarPisoEstado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modalEditarPiso')">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarEditarPiso()">
                    <i class="ti ti-device-floppy"></i> Guardar
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/ubicaciones.js') }}"></script>
@endpush
