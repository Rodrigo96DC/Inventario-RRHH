<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Modelo</th>
                <th>Descripción</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($query as $log)
            <tr>
                <td class="text-sm" style="white-space:nowrap;">
                    {{ $log->created_at->format('d/m/Y H:i') }}
                </td>
                <td>{{ $log->causer->name ?? 'Sistema' }}</td>
                <td>
                    @php
                        $colores = [
                            'created' => ['pill-green', 'Creado'],
                            'updated' => ['pill-blue',  'Actualizado'],
                            'deleted' => ['pill-red',   'Eliminado'],
                        ];
                        $color = $colores[$log->event] ?? ['pill-gray', $log->event];
                    @endphp
                    <span class="pill {{ $color[0] }}">{{ $color[1] }}</span>
                </td>
                <td>
                    <span class="pill pill-gray">{{ class_basename($log->subject_type) }}</span>
                </td>
                <td>{{ $log->description }}</td>
                <td>
                    <button class="btn btn-secondary btn-sm"
                        onclick="verDetalle({{ $log->id }})"
                        title="Ver detalle">
                        <i class="ti ti-eye"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:32px;color:var(--text-secondary);">
                    <i class="ti ti-shield-off" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Sin registros de auditoría
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>