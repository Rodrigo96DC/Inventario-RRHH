<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Funcionarios</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($areas as $a)
                <tr>
                    <td style="font-weight:500;">{{ $a->nombre }}</td>
                    <td>{{ $a->funcionarios()->count() }}</td>
                    <td>
                        <span class="pill {{ $a->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                            {{ $a->estado }}
                        </span>
                    </td>
                    <td>
                        <div class="flex-center gap-8">


                            @can('funcionarios.editar')
                                <button class="btn btn-secondary btn-sm" onclick="editarArea({{ $a->id }})">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm {{ $a->estado === 'Activo' ? 'btn-warning' : 'btn-primary' }}"
                                    onclick="toggleEstadoArea({{ $a->id }}, '{{ $a->estado }}')"
                                    title="{{ $a->estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}">
                                    <i class="ti ti-{{ $a->estado === 'Activo' ? 'toggle-right' : 'toggle-left' }}"></i>
                                </button>
                            @endcan
                            @can('funcionarios.eliminar')
                                @hasrole('Admin')
                                    <button class="btn btn-danger btn-sm" onclick="eliminarArea({{ $a->id }})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endhasrole
                            @endcan

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:32px;color:var(--text-secondary);">
                        <i class="ti ti-building-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No se encontraron áreas
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
