@extends('layouts.app')

@section('title', $proveedor->razon_social)
@section('page-title', 'Perfil del Proveedor')

@section('content')

{{-- Header --}}
<div class="card mb-16">
    <div class="flex-center gap-16">
        <div style="width:56px;height:56px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ti ti-truck" style="font-size:26px;"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:500;color:var(--text-primary);">
                {{ $proveedor->razon_social }}
            </div>
            <div class="text-secondary text-sm" style="margin-top:2px;">
                RUT: {{ $proveedor->rut }}
                @if($proveedor->contacto) — {{ $proveedor->contacto }} @endif
            </div>
            <div style="margin-top:8px;display:flex;align-items:center;gap:12px;">
                {{-- Estrellas promedio --}}
                <div style="display:flex;align-items:center;gap:6px;">
                    {!! $proveedor->estrella !!}
                    <span style="font-size:13px;color:var(--text-secondary);">
                        {{ number_format($promedio, 1) }} / 5.0
                        ({{ $proveedor->facturas()->whereHas('evaluacion')->count() }} evaluaciones)
                    </span>
                </div>
                <span class="pill {{ $proveedor->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                    {{ $proveedor->estado }}
                </span>
            </div>
        </div>
        <div class="flex-center gap-8">
            @can('proveedores.editar')
            <button class="btn btn-secondary" onclick="editarProveedor({{ $proveedor->id }})">
                <i class="ti ti-edit"></i> Editar
            </button>
            @endcan
            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="tabs-wrapper">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="cambiarTab('info', this)">
            <i class="ti ti-info-circle"></i> Información
        </button>
        <button class="tab-btn" onclick="cambiarTab('facturas', this)">
            <i class="ti ti-file-invoice"></i> Facturas
            <span style="background:var(--primary);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;margin-left:4px;">
                {{ $proveedor->facturas()->count() }}
            </span>
        </button>
        <button class="tab-btn" onclick="cambiarTab('historial', this)">
    <i class="ti ti-history"></i> Historial
</button>
        <button class="tab-btn" onclick="cambiarTab('evaluaciones', this)">
            <i class="ti ti-star"></i> Evaluaciones
        </button>
    </div>

    {{-- Tab info --}}
    <div class="tab-content active" id="tab-info">
        <div class="grid-2">
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-building"></i> Datos del proveedor
                </div>
                <table class="table-detail">
                    <tr><td class="detail-label">Razón social</td><td>{{ $proveedor->razon_social }}</td></tr>
                    <tr><td class="detail-label">RUT</td><td>{{ $proveedor->rut }}</td></tr>
                    <tr><td class="detail-label">Contacto</td><td>{{ $proveedor->contacto ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Teléfono</td><td>{{ $proveedor->telefono ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Email</td><td>{{ $proveedor->email ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Dirección</td><td>{{ $proveedor->direccion ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Estado</td><td>
                        <span class="pill {{ $proveedor->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                            {{ $proveedor->estado }}
                        </span>
                    </td></tr>
                </table>
            </div>
            <div class="card">
                <div class="card-title" style="margin-bottom:16px;">
                    <i class="ti ti-chart-bar"></i> Resumen
                </div>
                <table class="table-detail">
                    <tr><td class="detail-label">Total facturas</td><td>{{ $proveedor->facturas()->count() }}</td></tr>
                    <tr><td class="detail-label">Monto total</td><td>
                        ${{ number_format($proveedor->facturas()->sum('monto_total'), 0, ',', '.') }}
                    </td></tr>
                    <tr><td class="detail-label">Ítems asociados</td><td>
                        {{ \App\Models\ItemInventario::where('id_proveedor', $proveedor->id)->count() }}
                    </td></tr>
                    <tr><td class="detail-label">Calificación promedio</td><td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            {!! $proveedor->estrella !!}
                            <span class="text-secondary text-sm">{{ number_format($promedio, 1) }}</span>
                        </div>
                    </td></tr>
                    <tr><td class="detail-label">Creado por</td><td>{{ $proveedor->creadoPor->name ?? '—' }}</td></tr>
                    <tr><td class="detail-label">Fecha creación</td><td>{{ $proveedor->created_at->format('d/m/Y') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab facturas --}}
    <div class="tab-content" id="tab-facturas">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="ti ti-file-invoice"></i> Facturas
                </div>
                @can('proveedores.crear')
                <button class="btn btn-primary btn-sm" onclick="openModal('modalFactura')">
                    <i class="ti ti-plus"></i> Nueva factura
                </button>
                @endcan
            </div>
            <div id="facturasContainer">
                @include('proveedores.partials.facturas', compact('facturas', 'proveedor'))
            </div>
            <div id="facturasPaginacion" style="margin-top:16px;">
                @include('proveedores.partials.facturas_paginacion', compact('facturas', 'proveedor'))
            </div>
        </div>
    </div>

    {{-- Tab historial --}}
{{-- Tab historial --}}
<div class="tab-content" id="tab-historial">
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">
            <i class="ti ti-history"></i> Historial de cambios
        </div>
        @forelse($actividad as $log)
        <div style="padding:12px 0;border-bottom:0.5px solid var(--border);display:flex;gap:12px;align-items:flex-start;">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-{{ match($log->event) {
                    'created' => 'plus',
                    'updated' => 'edit',
                    'deleted' => 'trash',
                    default   => 'info-circle',
                } }}"></i>
            </div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:500;color:var(--text-primary);">
                        {{ $log->description }}
                    </span>

                    {{-- Link al elemento según tipo --}}
                    @if($log->subject_type === 'App\Models\Factura' && $log->subject)
                        <a href="{{ route('proveedores.perfil', $proveedor->id) }}#facturas"
                           onclick="event.preventDefault();cambiarTab('facturas', document.querySelector('[onclick*=\'facturas\']'));destacarFactura({{ $log->subject_id }})"
                           style="font-size:11px;padding:2px 8px;border-radius:20px;background:var(--primary-light);color:var(--primary);text-decoration:none;">
                            <i class="ti ti-file-invoice"></i>
                            Factura {{ $log->subject->numero_factura }}
                        </a>
                    @elseif($log->subject_type === 'App\Models\Proveedor')
                        <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46;">
                            <i class="ti ti-truck"></i> Proveedor
                        </span>
                    @endif
                </div>

                <div class="text-secondary text-sm" style="margin-top:2px;">
                    Por {{ $log->causer->name ?? 'Sistema' }} — {{ $log->created_at->format('d/m/Y H:i') }}
                </div>

                @if($log->properties->has('attributes') && $log->event === 'updated')
                <div style="margin-top:8px;">
                    @foreach($log->properties['attributes'] as $campo => $valor)
                        @if(isset($log->properties['old'][$campo]) && $log->properties['old'][$campo] != $valor)
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                            <strong>{{ $campo }}:</strong>
                            <span style="color:#991b1b;">{{ $log->properties['old'][$campo] ?? '—' }}</span>
                            → <span style="color:#065f46;">{{ $valor ?? '—' }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:32px;color:var(--text-secondary);">
            Sin historial de cambios
        </div>
        @endforelse
    </div>
</div>

    {{-- Tab evaluaciones --}}
    <div class="tab-content" id="tab-evaluaciones">
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="ti ti-star"></i> Historial de evaluaciones
            </div>
            @forelse($proveedor->facturas()->whereHas('evaluacion')->with('evaluacion')->get() as $f)
            <div style="padding:14px 0;border-bottom:0.5px solid var(--border);">
                <div class="flex-center gap-12">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-primary);">
                            Factura {{ $f->numero_factura }}
                        </div>
                        <div class="text-secondary text-sm">
                            {{ $f->fecha_factura->format('d/m/Y') }}
                            @if($f->monto_total)
                                — ${{ number_format($f->monto_total, 0, ',', '.') }}
                            @endif
                        </div>
                        @if($f->evaluacion->comentario)
                        <div style="font-size:13px;color:var(--text-primary);margin-top:6px;">
                            "{{ $f->evaluacion->comentario }}"
                        </div>
                        @endif
                    </div>
                    <div style="text-align:right;">
                        <div style="display:flex;gap:2px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="ti ti-star{{ $i <= $f->evaluacion->calificacion ? '-filled' : '' }}"
                                   style="color:{{ $i <= $f->evaluacion->calificacion ? '#f59e0b' : '#d1d5db' }};font-size:16px;"></i>
                            @endfor
                        </div>
                      <div class="text-secondary text-sm" style="margin-top:4px;">
    {{ $f->evaluacion->updated_at->format('d/m/Y') }}
    — Por {{ $f->evaluacion->creadoPor->name ?? 'Sin registrar' }}
</div>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:var(--text-secondary);">
                <i class="ti ti-star-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                Sin evaluaciones registradas
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal factura --}}
<div class="modal-overlay" id="modalFactura">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title" id="modalFacturaTitulo">Nueva Factura</span>
            <button class="modal-close" onclick="closeModal('modalFactura');limpiarModalFactura();">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="facturaId">
        <input type="hidden" id="facturaProveedorId" value="{{ $proveedor->id }}">
        <form id="formFactura" enctype="multipart/form-data">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">N° Factura <span class="required">*</span></label>
                    <input type="text" id="fNumero" class="form-control" placeholder="Ej: 001234">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha <span class="required">*</span></label>
                    <input type="date" id="fFecha" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Monto total</label>
                <input type="number" id="fMonto" class="form-control" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Archivo <span class="text-secondary text-sm">(PDF o imagen, máx 5MB)</span></label>
                <input type="file" id="fArchivo" class="form-control" accept=".pdf,.jpg,.png">
            </div>
            <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea id="fObservacion" class="form-control" rows="2"></textarea>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalFactura');limpiarModalFactura();">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarFactura()">
                <i class="ti ti-device-floppy"></i> Guardar
            </button>
        </div>
    </div>
</div>

{{-- Modal evaluar --}}
<div class="modal-overlay" id="modalEvaluar">
    <div class="modal" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title">Evaluar Factura</span>
            <button class="modal-close" onclick="closeModal('modalEvaluar')">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <input type="hidden" id="evaluarFacturaId">
        <form id="formEvaluar">
            <div class="form-group">
                <label class="form-label">Calificación <span class="required">*</span></label>
                <div style="display:flex;gap:8px;margin-top:4px;" id="estrellas">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="seleccionarEstrella({{ $i }})"
                        id="estrella{{ $i }}"
                        style="background:none;border:none;cursor:pointer;font-size:28px;color:#d1d5db;padding:0;transition:color 0.15s;">
                        <i class="ti ti-star-filled"></i>
                    </button>
                    @endfor
                </div>
                <input type="hidden" id="evaluarCalificacion" value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Comentario</label>
                <textarea id="evaluarComentario" class="form-control" rows="3"
                    placeholder="Comentario sobre la compra..."></textarea>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalEvaluar')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarEvaluacion()">
                <i class="ti ti-star"></i> Guardar evaluación
            </button>
        </div>
    </div>
</div>

{{-- Modal editar proveedor --}}
@include('proveedores.partials.modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/modules/proveedores.js') }}"></script>
    <script src="{{ asset('js/modules/proveedores.perfil.js') }}"></script>
@endpush