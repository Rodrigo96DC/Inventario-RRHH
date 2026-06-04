<!DOCTYPE html>
<html lang="es" data-theme="{{ auth()->user()->tema ?? 'light' }}" data-color="{{ auth()->user()->color ?? 'blue' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema RRHH') }} — @yield('title', 'Dashboard')</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

<div class="app-wrapper" id="appWrapper">

    {{-- Sidebar --}}
    @include('components.sidebar.sidebar')

    {{-- Contenido principal --}}
    <div class="main-content" id="mainContent">

        {{-- Topbar --}}
        @include('components.topbar.topbar')

        {{-- Área de contenido --}}
        <div class="content-area">
            {{-- Alertas globales --}}
            @include('components.ui.alerts')

            {{-- Contenido de la página --}}
            @yield('content')
        </div>

    </div>
</div>

{{-- JS principal --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

</body>
</html>
