<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Grado</th>
                <th>Funcionarios</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($cargos as $c)
                <tr>
                    <td style="font-weight:500;">{{ $c->nombre }}</td>
                    <td>{{ $c->grado ?? '—' }}</td>
                    <td>{{ $c->funcionarios()->count() }}</td>
                    <td>
                        <span class="pill {{ $c->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                            {{ $c->estado }}
                        </span>
                    </td>
                    <td>
                        <div class="flex-center gap-8">
                            @can('funcionarios.editar')
                                <button class="btn btn-secondary btn-sm" onclick="editarCargo({{ $c->id }})">
                                    <i class="ti ti-edit"></i>
                                </button>
                            @endcan

                            @can('funcionarios.editar')
                                <button class="btn btn-sm {{ $c->estado === 'Activo' ? 'btn-warning' : 'btn-primary' }}"
                                    onclick="toggleEstadoCargo({{ $c->id }}, '{{ $c->estado }}')"
                                    title="{{ $c->estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}">
                                    <i class="ti ti-{{ $c->estado === 'Activo' ? 'toggle-right' : 'toggle-left' }}"></i>
                                </button>
                            @endcan

                            @hasrole('Admin')
                                @hasrole('Admin')
                                    <button class="btn btn-danger btn-sm" onclick="eliminarCargo({{ $c->id }})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endhasrole
                            @endhasrole
        @can('funcionarios.editar')
<button class="btn btn-secondary btn-sm" onclick="verHistorial('App\\Models\\Cargo', {{ $c->id }}, '{{ $c->nombre }}')">
    <i class="ti ti-history"></i>
</button>
@endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:32px;color:var(--text-secondary);">
                        <i class="ti ti-briefcase-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No se encontraron cargos
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
