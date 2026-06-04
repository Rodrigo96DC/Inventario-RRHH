<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Funcionario</th>
                <th>RUT</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>Contrato</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($funcionarios as $f)
            <tr>
                <td>
                    <div class="flex-center gap-8">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            {{ $f->iniciales }}
                        </div>
                        <div>
                            <div style="font-weight:500;">{{ $f->nombre_completo }}</div>
                            <div class="text-secondary text-sm">{{ $f->email ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $f->rut }}</td>
                <td>{{ $f->area->nombre ?? '—' }}</td>
                <td>{{ $f->cargo->nombre ?? '—' }}</td>
                <td>{{ $f->tipo_contrato ?? '—' }}</td>
                <td>
                    @php
                        $color = match($f->estado) {
                            'Activo'     => 'pill-green',
                            'Inactivo'   => 'pill-red',
                            'Licencia'   => 'pill-amber',
                            'Vacaciones' => 'pill-blue',
                            default      => 'pill-gray',
                        };
                    @endphp
                    <span class="pill {{ $color }}">{{ $f->estado }}</span>
                </td>
         <td>
    <div class="flex-center gap-8">
               <a href="{{ route('funcionarios.perfil', $f->id) }}" class="btn btn-secondary btn-sm" title="Ver perfil">
    <i class="ti ti-eye"></i>
</a>
        @can('funcionarios.editar')
        <button class="btn btn-secondary btn-sm" onclick="editarFuncionario({{ $f->id }})" title="Editar">
            <i class="ti ti-edit"></i>
        </button>
        @endcan

        @can('funcionarios.editar')
        <button class="btn btn-sm {{ $f->estado === 'Inactivo' ? 'btn-primary' : 'btn-warning' }}"
            onclick="toggleEstado({{ $f->id }}, '{{ $f->estado }}')"
            title="{{ $f->estado === 'Inactivo' ? 'Habilitar' : 'Deshabilitar' }}">
            <i class="ti ti-{{ $f->estado === 'Inactivo' ? 'toggle-left' : 'toggle-right' }}"></i>
        </button>
        @endcan

        @hasrole('Admin')
        <button class="btn btn-danger btn-sm" onclick="eliminarFuncionario({{ $f->id }})" title="Eliminar">
            <i class="ti ti-trash"></i>
        </button>
        @endhasrole

 
    </div>
</td>


            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-users-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No se encontraron funcionarios
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>