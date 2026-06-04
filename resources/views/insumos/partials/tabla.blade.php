<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Unidad</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Método</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($insumos as $ins)
            <tr>
                <td>
                    <a href="{{ route('insumos.show', $ins->id) }}"
                        style="font-weight:500;color:var(--primary);text-decoration:none;">
                        {{ $ins->nombre }}
                    </a>
                    @if($ins->stock_bajo)
                    <span class="pill pill-amber" style="margin-left:6px;font-size:10px;">
                        <i class="ti ti-alert-triangle"></i> Stock bajo
                    </span>
                    @endif
                </td>
                <td>{{ $ins->categoria->nombre ?? '—' }}</td>
                <td>{{ $ins->unidad_medida }}</td>
                <td>
                    <span class="pill {{ $ins->color_stock }}">
                        {{ $ins->stock_actual }}
                    </span>
                </td>
                <td>{{ $ins->stock_minimo }}</td>
                <td>
                    <span class="pill pill-gray">{{ $ins->metodo_valuacion }}</span>
                </td>
                <td>
                    <span class="pill {{ $ins->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                        {{ $ins->estado }}
                    </span>
                </td>
                <td>
                    <div class="flex-center gap-8">
                        <a href="{{ route('insumos.show', $ins->id) }}"
                            class="btn btn-secondary btn-sm" title="Ver perfil">
                            <i class="ti ti-eye"></i>
                        </a>
                        @can('equipos.editar')
                        <button class="btn btn-secondary btn-sm"
                            onclick="editarInsumo({{ $ins->id }})" title="Editar">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-primary btn-sm"
                            onclick="abrirEntrada({{ $ins->id }}, '{{ $ins->nombre }}')"
                            title="Registrar entrada">
                            <i class="ti ti-package-import"></i>
                        </button>
                        <button class="btn btn-sm {{ $ins->estado === 'Activo' ? 'btn-warning' : 'btn-primary' }}"
                            onclick="toggleEstadoInsumo({{ $ins->id }}, '{{ $ins->estado }}')"
                            title="{{ $ins->estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}">
                            <i class="ti ti-{{ $ins->estado === 'Activo' ? 'toggle-right' : 'toggle-left' }}"></i>
                        </button>
                        @endcan
                        @hasrole('Admin')
                        <button class="btn btn-danger btn-sm"
                            onclick="eliminarInsumo({{ $ins->id }})" title="Eliminar">
                            <i class="ti ti-trash"></i>
                        </button>
                        @endhasrole
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-package-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No se encontraron insumos
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>