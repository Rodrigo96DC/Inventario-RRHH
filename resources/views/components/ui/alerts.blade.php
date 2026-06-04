{{-- Alertas de sesión --}}
@if(session('success'))
    <div class="alert alert-success" id="alertSuccess">
        <i class="ti ti-circle-check"></i>
        <span>{{ session('success') }}</span>
        <button onclick="closeAlert('alertSuccess')"><i class="ti ti-x"></i></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" id="alertError">
        <i class="ti ti-circle-x"></i>
        <span>{{ session('error') }}</span>
        <button onclick="closeAlert('alertError')"><i class="ti ti-x"></i></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning" id="alertWarning">
        <i class="ti ti-alert-triangle"></i>
        <span>{{ session('warning') }}</span>
        <button onclick="closeAlert('alertWarning')"><i class="ti ti-x"></i></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info" id="alertInfo">
        <i class="ti ti-info-circle"></i>
        <span>{{ session('info') }}</span>
        <button onclick="closeAlert('alertInfo')"><i class="ti ti-x"></i></button>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="alert alert-error" id="alertValidation">
        <i class="ti ti-circle-x"></i>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button onclick="closeAlert('alertValidation')"><i class="ti ti-x"></i></button>
    </div>
@endif
