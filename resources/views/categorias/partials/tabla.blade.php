<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Icono</th>
                <th>Nombre</th>
                <th>Items</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $cat)
            <tr>
                <td>
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;">
                        <i class="ti {{ $cat->icono ?? 'ti-box' }}" style="font-size:18px;"></i>
                    </div>
                </td>
                <td style="font-weight:500;">{{ $cat->nombre }}</td>
                <td>{{ $cat->items()->count() }}</td>
                <td>
                    <span class="pill {{ $cat->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                        {{ $cat->estado }}
                    </span>
                </td>
<td>
    <div class="flex-center gap-8">
        @can('equipos.editar')
        <button class="btn btn-secondary btn-sm" onclick="editarCategoria({{ $cat->id }})">
            <i class="ti ti-edit"></i>
        </button>
        <button class="btn btn-sm {{ $cat->estado === 'Activo' ? 'btn-warning' : 'btn-primary' }}"
            onclick="toggleEstadoCategoria({{ $cat->id }}, '{{ $cat->estado }}')"
            title="{{ $cat->estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}">
            <i class="ti ti-{{ $cat->estado === 'Activo' ? 'toggle-right' : 'toggle-left' }}"></i>
        </button>
        @endcan
        @hasrole('Admin')
        <button class="btn btn-danger btn-sm" onclick="eliminarCategoria({{ $cat->id }})" title="Eliminar">
            <i class="ti ti-trash"></i>
        </button>
        @endhasrole
    </div>
</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-category-2" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No se encontraron categorías
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>