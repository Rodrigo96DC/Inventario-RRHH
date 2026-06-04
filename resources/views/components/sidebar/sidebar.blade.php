<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-icon">
            <i class="ti ti-building"></i>
        </div>
        <span class="logo-text">{{ config('app.name', 'Sistema RRHH') }}</span>
    </div>

    {{-- Navegación --}}
    <nav class="sidebar-nav">

        {{-- Principal --}}
        <div class="nav-section">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        @can('funcionarios.ver')
        <a href="{{ route('funcionarios.index') }}"
           class="nav-item {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}">
            <i class="ti ti-users"></i>
            <span>Funcionarios</span>
        </a>
        @endcan

        {{-- RRHH
        @canany(['asistencia.ver','licencias.ver','vacaciones.ver','remuneraciones.ver'])
        <div class="nav-section">RRHH</div>
        @endcanany --}}
{{-- 
        @can('asistencia.ver')
        <a href="{{ route('asistencia.index') }}"
           class="nav-item {{ request()->routeIs('asistencia.*') ? 'active' : '' }}">
            <i class="ti ti-clock"></i>
            <span>Asistencia</span>
        </a>
        @endcan

        @can('licencias.ver')
        <a href="{{ route('licencias.index') }}"
           class="nav-item {{ request()->routeIs('licencias.*') ? 'active' : '' }}">
            <i class="ti ti-file-text"></i>
            <span>Licencias</span>
        </a>
        @endcan --}}

        {{-- @can('vacaciones.ver')
        <a href="{{ route('vacaciones.index') }}"
           class="nav-item {{ request()->routeIs('vacaciones.*') ? 'active' : '' }}">
            <i class="ti ti-beach"></i>
            <span>Vacaciones</span>
        </a>
        @endcan

        @can('remuneraciones.ver')
        <a href="{{ route('remuneraciones.index') }}"
           class="nav-item {{ request()->routeIs('remuneraciones.*') ? 'active' : '' }}">
            <i class="ti ti-cash"></i>
            <span>Remuneraciones</span>
        </a>
        @endcan --}}

        {{-- Inventario --}}
     @can('equipos.ver')
<a href="{{ route('inventario.index') }}"
   class="nav-item {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
    <i class="ti ti-box"></i>
    <span>Inventario</span>
</a>
@endcan
@can('equipos.ver')
<a href="{{ route('insumos.index') }}"
   class="nav-item {{ request()->routeIs('insumos.*') ? 'active' : '' }}">
    <i class="ti ti-package"></i>
    <span>Insumos</span>
</a>
@endcan
@can('proveedores.ver')
<a href="{{ route('proveedores.index') }}"
   class="nav-item {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
    <i class="ti ti-truck"></i>
    <span>Proveedores</span>
</a>
@endcan

<a href="{{ route('ubicaciones.index') }}"
   class="nav-item {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}">
    <i class="ti ti-map-pin"></i>
    <span>Ubicaciones</span>
</a>

@can('auditoria.ver')
<a href="{{ route('auditoria.index') }}"
   class="nav-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
    <i class="ti ti-shield-check"></i>
    <span>Auditoría</span>
</a>
@endcan
{{--
        @can('equipos.ver')
        <a href="{{ route('equipos.index') }}"
           class="nav-item {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
            <i class="ti ti-cpu"></i>
            <span>Equipos</span>
        </a>
        @endcan

        @can('insumos.ver')
        <a href="{{ route('insumos.index') }}"
           class="nav-item {{ request()->routeIs('insumos.*') ? 'active' : '' }}">
            <i class="ti ti-box"></i>
            <span>Insumos</span>
        </a>
        @endcan

        @can('proveedores.ver')
        <a href="{{ route('proveedores.index') }}"
           class="nav-item {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
            <i class="ti ti-truck"></i>
            <span>Proveedores</span>
        </a>
        @endcan --}}

        {{-- Sistema --}}
        {{-- <div class="nav-section">Sistema</div>

        @can('reportes.ver')
        <a href="{{ route('reportes.index') }}"
           class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar"></i>
            <span>Reportes</span>
        </a>
        @endcan

        @can('auditoria.ver')
        <a href="{{ route('auditoria.index') }}"
           class="nav-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
            <i class="ti ti-shield-check"></i>
            <span>Auditoría</span>
        </a>
        @endcan

        <a href="{{ route('configuracion.index') }}"
           class="nav-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i>
            <span>Configuración</span>
        </a> --}}
<div class="nav-section">Configuración</div>

<a href="{{ route('cargos.index') }}"
   class="nav-item {{ request()->routeIs('cargos.*') ? 'active' : '' }}">
    <i class="ti ti-briefcase"></i>
    <span>Cargos</span>
</a>

<a href="{{ route('areas.index') }}"
   class="nav-item {{ request()->routeIs('areas.*') ? 'active' : '' }}">
    <i class="ti ti-building"></i>
    <span>Áreas</span>
</a>
<a href="{{ route('categorias.index') }}"
   class="nav-item {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
    <i class="ti ti-category-2"></i>
    <span>Categorías</span>
</a>
    </nav>

    {{-- Info usuario al fondo --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
            <span class="sidebar-user-role">{{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}</span>
        </div>
    </div>

</aside>
