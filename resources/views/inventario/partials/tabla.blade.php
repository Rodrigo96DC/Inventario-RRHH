<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Ítem</th>
                <th>Categoría</th>
                <th>Código</th>
                <th>Marca / Modelo</th>
                <th>Estado</th>
                <th>Asignado a</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>
                    <div class="flex-center gap-8">
                        @if($item->imagen)
                            <img src="{{ Storage::url($item->imagen) }}"
                                 style="width:36px;height:36px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                        @else
                            <div style="width:36px;height:36px;border-radius:6px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="ti {{ $item->categoria->icono ?? 'ti-box' }}" style="font-size:18px;"></i>
                            </div>
                        @endif
                        <div>
                            <div style="font-weight:500;">{{ $item->nombre }}</div>
                            <div class="text-secondary text-sm">{{ $item->numero_serie ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $item->categoria->nombre }}</td>
                <td>{{ $item->codigo_interno ?? '—' }}</td>
                <td>
                    <div>{{ $item->marca ?? '—' }}</div>
                    <div class="text-secondary text-sm">{{ $item->modelo ?? '—' }}</div>
                </td>
                <td>
                    <span class="pill {{ $item->color_estado }}">{{ $item->estado }}</span>
                </td>
                <td>
                    @if($item->asignacionActiva)
                        <a href="{{ route('funcionarios.perfil', $item->asignacionActiva->funcionario->id) }}"
                           style="color:var(--primary);font-size:13px;">
                            {{ $item->asignacionActiva->funcionario->nombre_completo }}
                        </a>
                    @else
                        <span class="text-secondary text-sm">Sin asignar</span>
                    @endif
                </td>
                <td>
                    <div class="flex-center gap-8">
                        <a href="{{ route('inventario.perfil', $item->id) }}"
                           class="btn btn-secondary btn-sm" title="Ver perfil">
                            <i class="ti ti-eye"></i>
                        </a>
                        @can('equipos.editar')
                        <button class="btn btn-secondary btn-sm"
                                onclick="editarItem({{ $item->id }})" title="Editar">
                            <i class="ti ti-edit"></i>
                        </button>
                        @endcan
                        @hasrole('Admin')
                        <button class="btn btn-danger btn-sm"
                                onclick="eliminarItem({{ $item->id }})" title="Eliminar">
                            <i class="ti ti-trash"></i>
                        </button>
                        @endhasrole
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-box-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No se encontraron ítems en el inventario
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>