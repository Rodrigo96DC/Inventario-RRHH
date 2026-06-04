<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema RRHH') }} — @yield('title', 'Iniciar sesión')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body class="auth-body">

    <div class="auth-wrapper">
        <div class="auth-card">

            {{-- Logo --}}
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class="ti ti-building"></i>
                </div>
                <span>{{ config('app.name', 'Sistema RRHH') }}</span>
            </div>

            {{-- Contenido --}}
            @yield('content')

        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

</body>
</html>
