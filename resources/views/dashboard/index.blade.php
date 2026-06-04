@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h1>Bienvenido, {{ auth()->user()->name }}</h1>
        <p>Resumen general del sistema — {{ now()->format('d/m/Y') }}</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">

    @can('funcionarios.ver')
    <div class="stat-card">
        <div class="stat-label">Funcionarios activos</div>
        <div class="stat-value">{{ $stats['funcionarios'] ?? 0 }}</div>
        <span class="stat-badge badge-up">
            <i class="ti ti-users" style="font-size:11px;"></i> Total
        </span>
    </div>
    @endcan

    {{-- @can('equipos.ver')
    <div class="stat-card">
        <div class="stat-label">Equipos registrados</div>
        <div class="stat-value">{{ $stats['equipos'] ?? 0 }}</div>
        <span class="stat-badge badge-up">
            <i class="ti ti-cpu" style="font-size:11px;"></i> Inventario
        </span>
    </div>
    @endcan

    @can('licencias.ver')
    <div class="stat-card">
        <div class="stat-label">Licencias activas</div>
        <div class="stat-value">{{ $stats['licencias'] ?? 0 }}</div>
        <span class="stat-badge badge-down">
            <i class="ti ti-file-text" style="font-size:11px;"></i> En curso
        </span>
    </div>
    @endcan

    @can('insumos.ver')
    <div class="stat-card">
        <div class="stat-label">Insumos en stock</div>
        <div class="stat-value">{{ $stats['insumos'] ?? 0 }}</div>
        <span class="stat-badge badge-up">
            <i class="ti ti-box" style="font-size:11px;"></i> Disponibles
        </span>
    </div>
    @endcan --}}

</div>

{{-- Cards de resumen --}}
<div class="grid-2">

    @can('funcionarios.ver')
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="ti ti-users"></i>
                Últimos funcionarios
            </div>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary btn-sm">
                Ver todos
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Área</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimosFuncionarios ?? [] as $f)
                    <tr>
                        <td>
                            <div class="flex-center gap-8">
                                <div style="width:28px;height:28px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:11px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    {{ strtoupper(substr($f->nombre, 0, 2)) }}
                                </div>
                                {{ $f->nombre }}
                            </div>
                        </td>
                        <td class="text-secondary">{{ $f->area->nombre ?? '—' }}</td>
                        <td>
                            <span class="pill {{ $f->estado === 'Activo' ? 'pill-green' : 'pill-red' }}">
                                {{ $f->estado }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:var(--text-secondary);padding:20px;">
                            Sin funcionarios registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcan

    @can('equipos.ver')
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="ti ti-cpu"></i>
                Equipos recientes
            </div>
            {{-- <a href="{{ route('equipos.index') }}" class="btn btn-secondary btn-sm">
                Ver todos
            </a> --}}
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Inventario</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimosEquipos ?? [] as $e)
                    <tr>
                        <td>{{ $e->inventario }}</td>
                        <td class="text-secondary text-sm">{{ $e->equipo->tipo ?? '—' }}</td>
                        <td>
                            @php
                                $color = match($e->estado) {
                                    'Disponible' => 'pill-green',
                                    'Asignado'   => 'pill-blue',
                                    'Mantención' => 'pill-amber',
                                    default      => 'pill-gray',
                                };
                            @endphp
                            <span class="pill {{ $color }}">{{ $e->estado }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:var(--text-secondary);padding:20px;">
                            Sin equipos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcan

</div>

@endsection
