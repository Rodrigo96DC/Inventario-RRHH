<header class="topbar" id="topbar">

    {{-- Botón colapsar sidebar --}}
    <button class="collapse-btn" id="collapseBtn" title="Colapsar menú">
        <i class="ti ti-menu-2"></i>
    </button>

    {{-- Título de la página --}}
    <div class="topbar-title">
        @yield('page-title', 'Dashboard')
    </div>

    {{-- Breadcrumb (opcional por página) --}}
    <div class="topbar-breadcrumb">
        @yield('breadcrumb')
    </div>

    <div class="topbar-spacer"></div>

    {{-- Acciones --}}
    <div class="topbar-actions">


        {{-- Notificaciones --}}
        <div style="position:relative;" id="notifContainer">
            <button class="topbar-btn" onclick="toggleNotificaciones()" title="Notificaciones">
                <i class="ti ti-bell"></i>
                @if (isset($notificacionesCount) && $notificacionesCount > 0)
                    <span class="badge-dot"></span>
                @endif
            </button>

            <div id="notifDropdown"
                style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:var(--card-bg);border:0.5px solid var(--border);border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.1);z-index:200;overflow:hidden;">
                <div
                    style="padding:12px 16px;border-bottom:0.5px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;font-weight:500;color:var(--text-primary);">Notificaciones</span>
                    @if (isset($notificacionesCount) && $notificacionesCount > 0)
                        <button onclick="marcarTodasLeidas()"
                            style="font-size:11px;color:var(--primary);background:none;border:none;cursor:pointer;">
                            Marcar todas como leídas
                        </button>
                    @endif
                </div>
                {{-- <div style="font-size:11px;color:red;">
    Notif count: {{ $notificacionesCount ?? 'NO DEFINIDA' }} |
    Notif total: {{ isset($notificaciones) ? $notificaciones->count() : 'NO DEFINIDA' }}
</div> --}}

                <div style="max-height:320px;overflow-y:auto;">
                    @forelse($notificaciones ?? [] as $notif)
                        <a href="{{ $notif->tipo === 'contrato_vencimiento' ? route('funcionarios.perfil', $notif->id_ref) : '#' }}"
                            onclick="marcarLeida({{ $notif->id }})"
                            style="display:block;padding:12px 16px;border-bottom:0.5px solid var(--border);background:{{ $notif->leido ? 'transparent' : 'var(--primary-light)' }};text-decoration:none;">
                            <div style="font-size:13px;font-weight:500;color:var(--text-primary);">
                                {{ $notif->titulo }}
                            </div>
                            <div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">
                                {{ $notif->mensaje }}
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                                {{ $notif->created_at->diffForHumans() }}
                            </div>
                        </a>
                    @empty
                        <div style="padding:24px;text-align:center;color:var(--text-secondary);font-size:13px;">
                            Sin notificaciones pendientes
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Toggle tema --}}
        <button class="topbar-btn" id="themeBtn" title="Cambiar tema" onclick="toggleTheme()">
            <i class="ti ti-sun" id="themeIcon"></i>
        </button>


        {{-- Avatar + dropdown usuario --}}
        <div class="user-menu" id="userMenu">
            <button class="user-avatar" onclick="toggleUserMenu()">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </button>

            <div class="user-dropdown" id="userDropdown">
                <div class="user-dropdown-header">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->email }}</span>
                </div>
                <div class="user-dropdown-divider"></div>
                <a href="{{ route('configuracion.index') }}" class="user-dropdown-item">
                    <i class="ti ti-settings"></i> Configuración
                </a>
                {{-- <a href="{{ route('perfil.index') }}" class="user-dropdown-item">
                    <i class="ti ti-user"></i> Mi perfil
                </a> --}}
                <div class="user-dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="user-dropdown-item user-dropdown-danger">
                        <i class="ti ti-logout"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
