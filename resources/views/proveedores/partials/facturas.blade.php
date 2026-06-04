<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>N° Factura</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Ítems</th>
                <th>Evaluación</th>
                <th>Archivo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($facturas as $f)
            <tr>
                <td style="font-weight:500;">{{ $f->numero_factura }}</td>
                <td>{{ $f->fecha_factura->format('d/m/Y') }}</td>
                <td>{{ $f->monto_total ? '$'.number_format($f->monto_total, 0, ',', '.') : '—' }}</td>
                <td>{{ $f->items()->count() }}</td>
                <td>
                    @if($f->evaluacion)
                        <div style="display:flex;gap:2px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="ti ti-star{{ $i <= $f->evaluacion->calificacion ? '-filled' : '' }}"
                                   style="color:{{ $i <= $f->evaluacion->calificacion ? '#f59e0b' : '#d1d5db' }};font-size:14px;"></i>
                            @endfor
                        </div>
                    @else
                        <span class="text-secondary text-sm">Sin evaluar</span>
                    @endif
                </td>
                <td>
                    @if($f->archivo)
                        <a href="{{ Storage::url($f->archivo) }}" target="_blank"
                           class="btn btn-secondary btn-sm">
                            <i class="ti ti-file"></i>
                        </a>
                    @else
                        <span class="text-secondary text-sm">—</span>
                    @endif
                </td>
                <td>
                    <div class="flex-center gap-8">
                        @can('proveedores.editar')
                        <button class="btn btn-secondary btn-sm"
                            onclick="editarFactura({{ $f->id }}, '{{ $f->numero_factura }}', '{{ $f->fecha_factura->format('Y-m-d') }}', '{{ $f->monto_total }}', '{{ $f->observacion }}')"
                            title="Editar">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm {{ $f->evaluacion ? 'btn-warning' : 'btn-primary' }}"
                            onclick="abrirEvaluar({{ $f->id }}, {{ $f->evaluacion?->calificacion ?? 0 }}, '{{ addslashes($f->evaluacion?->comentario ?? '') }}')"
                            title="{{ $f->evaluacion ? 'Editar evaluación' : 'Evaluar' }}">
                            <i class="ti ti-star"></i>
                        </button>
                        @endcan
                        @hasrole('Admin')
                        <button class="btn btn-danger btn-sm"
                            onclick="eliminarFactura({{ $f->id }})"
                            title="Eliminar">
                            <i class="ti ti-trash"></i>
                        </button>
                        @endhasrole
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-file-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Sin facturas registradas
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>