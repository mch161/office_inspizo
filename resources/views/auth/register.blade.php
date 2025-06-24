@extends('adminlte::auth.register')

@section('css')
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection

@section('js')
<script>
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        let toggleIconId = (fieldId === 'password') ? 'togglePasswordIconPassword' : 'togglePasswordIconConfirm';
        const toggleIcon = document.getElementById(toggleIconId);

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
@endsection

@section('auth_body')
<form method="POST" action="{{ route('register.submit') }}">
    @csrf

    <div class="form-group">
        <input type="text" id="nama_pelanggan" name="nama_pelanggan"
            class="form-control @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}"
            placeholder="Nama" autocomplete="nama_pelanggan" autofocus>
        @error('nama_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="number" id="telp_pelanggan" name="telp_pelanggan"
            class="form-control @error('telp_pelanggan') is-invalid @enderror" value="{{ old('telp_pelanggan') }}"
            placeholder="Nomor Telepon" autocomplete="telp_pelanggan">
        @error('telp_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="text" id="alamat_pelanggan" name="alamat_pelanggan"
            class="form-control @error('alamat_pelanggan') is-invalid @enderror" value="{{ old('alamat_pelanggan') }}"
            placeholder="Alamat" autocomplete="alamat_pelanggan">
        @error('alamat_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="text" id="nama_perusahaan" name="nama_perusahaan"
            class="form-control @error('nama_perusahaan') is-invalid @enderror" value="{{ old('nama_perusahaan') }}"
            placeholder="Nama Perusahaan" autocomplete="name" autofocus>
        @error('nama_perusahaan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="number" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror"
            value="{{ old('nik') }}" placeholder="NIK" autocomplete="nik">
        @error('nik')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
            value="{{ old('username') }}" placeholder="Username" autocomplete="username">
        @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}" placeholder="Email" autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <div class="input-group">
            <input type="password" id="password" name="password"
                class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}"
                placeholder="Password" autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility('password')">
                    <span id="togglePasswordIconPassword" class="fas fa-eye"></span>
                </div>
            </div>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <div class="input-group">
            <input type="password" id="password-confirm" name="password_confirmation" class="form-control"
                value="{{ old('password_confirmation') }}" placeholder="Confirm Password" autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text" style="cursor: pointer;"
                    onclick="togglePasswordVisibility('password-confirm')">
                    <span id="togglePasswordIconConfirm" class="fas fa-eye"></span>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Register</button>
</form>
@stop