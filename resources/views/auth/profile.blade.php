@extends('adminlte::page')

@section('title', 'My Profile')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1>My Profile</h1>
@stop

@section('css')
<style>
    .profile-pic-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
        border-radius: 50%;
    }

    .profile-user-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .profile-pic-container .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
    }

    .profile-pic-container:hover .upload-overlay {
        opacity: 1;
    }

    .upload-overlay span {
        font-size: 0.8rem;
        margin-top: 5px;
    }

    .form-control[readonly] {
        background-color: transparent;
        border: 0;
        box-shadow: none;
        padding-left: 0;
    }

    .toggle-password-icon {
        cursor: pointer;
    }
</style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <form id="profile-pic-form" enctype="multipart/form-data" class="d-none">
                        <input type="file" name="foto" id="foto-upload">
                    </form>
                    <div class="profile-pic-container" id="profile-pic-trigger">
                        <img class="profile-user-img img-fluid img-circle" id="profile-pic-preview"
                            src="{{ $user->foto ? Storage::url($user->foto) : asset('default.png') }}"
                            alt="User profile picture">
                        <div class="upload-overlay">
                            <i class="fas fa-camera"></i>
                            <span>Change</span>
                        </div>
                    </div>
                </div>
                <h3 class="profile-username text-center mt-3 mb-0">{{ $user->username }}</h3>
                <p class="text-muted text-center mt-0 mb-1">{{ $user->role }}</p>
                <p class="text-muted text-center" style="font-size: 0.9rem;">{{ $user->alamat }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Personal Information</h3>
                <div class="card-tools">
                    <button type="button" id="tombol-edit" class="btn btn-sm btn-warning">Edit</button>
                    <button type="submit" form="profile-form" id="tombol-simpan" class="btn btn-sm btn-success"
                        style="display: none;">Simpan</button>
                    <button type="button" id="tombol-batal" class="btn btn-sm btn-secondary"
                        style="display: none;">Batal</button>
                </div>
            </div>
            <div class="card-body">
                <form id="profile-form">

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" value="{{ $user->nama ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Username (Nama Panggilan)</label>
                            <input type="text" class="form-control" name="username" value="{{ $user->username ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Alamat</label>
                            <input type="text" class="form-control" name="alamat" value="{{ $user->alamat ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>NIP</label>
                            <input type="text" class="form-control" name="nip" value="{{ $user->nip ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>NIK</label>
                            <input type="text" class="form-control" name="nik" value="{{ $user->nik ?? '-' }}" readonly>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contact Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" class="form-control" name="telp" value="{{ $user->telp ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ganti Password</h3>
            </div>
            <div class="card-body">
                <div>
                    <form action="{{ route('ganti-password') }}" method="POST">
                        @csrf
                        <div class="col-md-4 form-group">
                            <label>Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_lama') is-invalid @enderror"
                                    name="password_lama" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-eye toggle-password-icon"></span>
                                    </div>
                                </div>
                            </div>
                            @error('password_lama')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-eye toggle-password-icon"></span>
                                    </div>
                                </div>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password_confirmation" required
                                    autocomplete="new-password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-eye toggle-password-icon"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <button type="submit" class="btn btn-primary">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @stop

    @section('js')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            const formInputs = $('#profile-form input');

            $('#tombol-edit').on('click', function () {
                toggleEditMode(true);
            });

            $('#tombol-batal').on('click', function () {
                toggleEditMode(false);
            });

            $('#profile-form').on('submit', function (e) {
                e.preventDefault();
                saveProfileData();
            });

            function toggleEditMode(enable) {
                if (enable) {
                    formInputs.removeAttr('readonly');
                    $('#tombol-edit').hide();
                    $('#tombol-simpan, #tombol-batal').show();
                } else {
                    formInputs.attr('readonly', true);
                    $('#tombol-edit').show();
                    $('#tombol-simpan, #tombol-batal').hide();
                }
            }

            $('.toggle-password-icon').on('click', function () {
                const passwordInput = $(this).closest('.input-group').find('input');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);

                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('.toggle-password').on('click', function () {
                const passwordInput = $(this).prev('input');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);

                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            function saveProfileData() {
                $.ajax({
                    url: "{{ route('profile.update') }}",
                    type: 'POST',
                    data: $('#profile-form').serialize(),
                    success: function (response) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.success,
                            icon: 'success'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = 'Gambar gagal diupload, silahkan coba lagi atau hubungi admin.';
                        if (errors) {
                            errorMsg = Object.values(errors).map(e => e[0]).join('<br>');
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }

            $('#profile-pic-trigger').on('click', function () {
                $('#foto-upload').click();
            });

            $('#foto-upload').on('change', function () {
                let formData = new FormData();
                formData.append('foto', $(this)[0].files[0]);

                $.ajax({
                    url: "{{ route('profile.update') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.success,
                            icon: 'success'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        let errorMsg = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.foto)
                            ? xhr.responseJSON.errors.foto[0]
                            : 'An error occurred.';
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            });
        });
        @if (session()->has('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'success',
                text: '{{ session('success') }}',
            })
        @endif
        @if (session()->has('error'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'error',
                text: '{{ session('error') }}',
            })
        @endif
    </script>
    @stop