<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Razón Social</th>
                <th>RUT</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Facturas</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($proveedores as $p)
            <tr>
                <td style="font-weight:500;">{{ $p->razon_social }}</td>
                <td>{{ $p->rut }}</td>
                <td>{{ $p->contacto ?? '—' }}</td>
                <td>{{ $p->telefono ?? '—' }}</td>
                <td>{{ $p->email ?? '—' }}</td>
                <td>{{ $p->facturas()->count() }}</td>
                <td>
                    <span class="pill {{ $p->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                        {{ $p->estado }}
                    </span>
                </td>
                <td>
                    <div class="flex-center gap-8">
                                             <a href="{{ route('proveedores.perfil', $p->id) }}" class="btn btn-secondary btn-sm" title="Ver perfil">
    <i class="ti ti-eye"></i>
</a>
                        @can('proveedores.editar')
                        <button class="btn btn-secondary btn-sm" onclick="editarProveedor({{ $p->id }})">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm {{ $p->estado === 'Activo' ? 'btn-warning' : 'btn-primary' }}"
                            onclick="toggleEstadoProveedor({{ $p->id }}, '{{ $p->estado }}')"
                            title="{{ $p->estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}">
                            <i class="ti ti-{{ $p->estado === 'Activo' ? 'toggle-right' : 'toggle-left' }}"></i>
                        </button>
                        @endcan
                        @hasrole('Admin')
                        <button class="btn btn-danger btn-sm" onclick="eliminarProveedor({{ $p->id }})">
                            <i class="ti ti-trash"></i>
                        </button>
                        @endhasrole
   
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-truck-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No se encontraron proveedores
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>