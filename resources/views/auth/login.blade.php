@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')

<h2 style="font-size:18px;font-weight:500;color:var(--text-primary);margin-bottom:6px;">
    Iniciar sesión
</h2>
<p style="font-size:13px;color:var(--text-secondary);margin-bottom:24px;">
    Ingresa tus credenciales para acceder al sistema
</p>

@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="ti ti-circle-x"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label" for="email">
            Correo electrónico <span class="required">*</span>
        </label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="{{ old('email') }}"
            placeholder="correo@ejemplo.cl"
            required
            autofocus
        >
    </div>

    <div class="form-group">
        <label class="form-label" for="password">
            Contraseña <span class="required">*</span>
        </label>
        <div style="position:relative;">
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required
                style="padding-right:42px;"
            >
            <button
                type="button"
                onclick="togglePassword()"
                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-secondary);"
            >
                <i class="ti ti-eye" id="eyeIcon" style="font-size:16px;"></i>
            </button>
        </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
            <input type="checkbox" name="remember" style="width:14px;height:14px;">
            Recordarme
        </label>
        @if(Route::has('password.request'))
        <a href="{{ route('password.request') }}"
           style="font-size:13px;color:var(--primary);">
            ¿Olvidaste tu contraseña?
        </a>
        @endif
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
        <i class="ti ti-login"></i>
        Ingresar
    </button>

</form>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'ti ti-eye-off' : 'ti ti-eye';
}
</script>
@endpush

@endsection
