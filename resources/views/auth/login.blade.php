@extends('adminlte::auth.login')

@section('js')
<script>
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById('togglePasswordIcon');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@stop

@section('auth_body')
<form action="{{ route('login.submit') }}" method="post">
    @csrf

    <div class="input-group mb-3">
        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
            value="{{ old('login') }}" placeholder="{{ __('Email, Username, or Phone') }}" required autofocus>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @error('login')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-3">
        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="{{ __('adminlte::adminlte.password') }}">
        <div class="input-group-append">
            <div class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility('password')">
                <span id="togglePasswordIcon" class="fas fa-eye {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
            <div class="input-group-text">
                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">{{ __('Remember Me') }}</label>
            </div>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">{{ __('Sign In') }}</button>
        </div>
    </div>
</form>
@stop