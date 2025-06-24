<form method="POST" action="{{ route('register.submit') }}">
    @csrf

    <div class="form-group">
        <label for="nama_pelanggan">Nama</label>
        <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}" autocomplete="nama_pelanggan" autofocus>
        @error('nama_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="telp_pelanggan">Nomor Telepon</label>
        <input type="number" id="telp_pelanggan" name="telp_pelanggan" class="form-control @error('telp_pelanggan') is-invalid @enderror" value="{{ old('telp_pelanggan') }}" autocomplete="telp_pelanggan">
        @error('telp_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="alamat_pelanggan">Alamat</label>
        <input type="text" id="alamat_pelanggan" name="alamat_pelanggan" class="form-control @error('alamat_pelanggan') is-invalid @enderror" value="{{ old('alamat_pelanggan') }}" autocomplete="alamat_pelanggan">
        @error('alamat_pelanggan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="nama_perusahaan">Nama Perusahaan</label>
        <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control @error('nama_perusahaan') is-invalid @enderror" value="{{ old('nama_perusahaan') }}" autocomplete="name" autofocus>
        @error('nama_perusahaan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="nik">NIK</label>
        <input type="number" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" autocomplete="nik">
        @error('nik')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" autocomplete="username">
        @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input type="password" id="password-confirm" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}" autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
</form>