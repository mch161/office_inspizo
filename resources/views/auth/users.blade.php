@extends('adminlte::page')

@section('title', 'Users')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Users</h1>
@stop

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

@section('content')
<div class="d-flex justify-content-end">
    <x-adminlte-button label="Tambahkan User" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
</div>
<div class="d-flex justify-content-end">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="TampilkanSemuaUser" name="TampilkanSemuaUser">
        <label class="custom-control-label" for="TampilkanSemuaUser">Tampilkan Semua User</label>
    </div>
</div>

<x-adminlte-modal id="modalTambah" title="Tambahkan User" theme="success" icon="fas fa-user" size='lg'>
    <form action="{{ route('users.store') }}" method="POST" id="userForm">
        @csrf
        <x-adminlte-input name="nama" label="Nama Lengkap" placeholder="Nama Lengkap" value="{{ old('nama') }}" />
        <x-adminlte-input name="username" label="Username" placeholder="Username" value="{{ old('username') }}" />
        <x-adminlte-input name="telp" label="Telepon" placeholder="Telepon" type="number" value="{{ old('telp') }}" />
        <x-adminlte-textarea name="alamat" label="Alamat" placeholder="Alamat" value="{{ old('alamat') }}" />
        <x-adminlte-input name="nip" label="NIP" placeholder="NIP" type="number" value="{{ old('nip') }}" />
        <x-adminlte-input name="nik" label="NIK" placeholder="NIK" type="number" value="{{ old('nik') }}" />
        <x-adminlte-select name="role" label="Role" empty-option="Pilih role..." value="{{ old('role') }}">
            <option value="" disabled selected>Pilih role...</option>
            <option value="superadmin">Superadmin</option>
            <option value="karyawan">Karyawan</option>
            <option value="magang">Magang</option>
        </x-adminlte-select>
        <x-adminlte-input name="email" label="Email" placeholder="Email" value="{{ old('email') }}" />
        <x-adminlte-input name="password" label="Password" type="password" placeholder="Password"
            value="{{ old('password') }}" />
        <x-adminlte-input name="password_confirmation" label="Konfirmasi Password" type="password"
            placeholder="Konfirmasi Password" value="{{ old('password_confirmation') }}" />

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="userForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>
<x-adminlte-modal id="modalEdit" title="Edit User" theme="primary" icon="fas fa-edit" size='lg'>
    <form method="POST" id="form-edit-user">
        @csrf
        @method('PUT')
        <x-adminlte-input name="edit-nama" id="edit-nama" label="Nama Lengkap" placeholder="Nama Lengkap"
            value="{{ old('edit-nama') }}" />
        <x-adminlte-input name="edit-username" id="edit-username" label="Username" placeholder="Username"
            value="{{ old('edit-username') }}" />
        <x-adminlte-input name="edit-telp" id="edit-telp" label="Telepon" placeholder="Telepon" type="number"
            value="{{ old('edit-telp') }}" />
        <x-adminlte-textarea name="alamat" id="edit-alamat" label="Alamat"
            placeholder="Alamat">{{ old('alamat') }}</x-adminlte-textarea>
        <x-adminlte-input name="edit-nip" id="edit-nip" label="NIP" placeholder="NIP" type="number"
            value="{{ old('edit-nip') }}" />
        <x-adminlte-input name="edit-nik" id="edit-nik" label="NIK" placeholder="NIK" type="number"
            value="{{ old('edit-nik') }}" />
        <x-adminlte-select name="edit-role" id="edit-role" label="Role" empty-option="Pilih role..."
            value="{{ old('edit-role') }}">
            <option value="" disabled>Pilih role...</option>
            <option value="superadmin">Superadmin</option>
            <option value="karyawan">Karyawan</option>
            <option value="magang">Magang</option>
        </x-adminlte-select>
        <x-adminlte-select name="edit-status" id="edit-status" label="Status" empty-option="Pilih status..."
            value="{{ old('edit-status') }}">
            <option value="" disabled>Pilih status...</option>
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </x-adminlte-select>
        <x-adminlte-input name="edit-email" id="edit-email" label="Email" placeholder="Email"
            value="{{ old('edit-email') }}" />
        <x-adminlte-input name="edit-password" id="edit-password" label="Password" type="password"
            placeholder="Password" value="{{ old('edit-password') }}" />
        <x-adminlte-input name="edit-password_confirmation" id="edit-password_confirmation" label="Konfirmasi Password"
            type="password" placeholder="Konfirmasi Password" value="{{ old('edit-password_confirmation') }}" />

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="form-edit-user" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<table id="usersTable" class="table table-bordered table-striped ">
    <thead>
        <tr>
            <th width="1%">No</th>
            <th width="150px">Nama Lengkap</th>
            <th width="100px">Username</th>
            <th width="150px" class="dt-left">Telepon</th>
            <th width="150px">Alamat</th>
            <th width="150px" class="dt-left">NIP</th>
            <th width="150px" class="dt-left">NIK</th>
            <th width="150px">Email</th>
            <th width="50px">Role</th>
            <th width="50px">Status</th>
            <th width="150px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($user as $u)
            <tr data-status="{{ $u->status ?? '0' }}">
                <td></td>
                <td>{{ $u->nama ?? '-' }}</td>
                <td>{{ $u->username ?? '-' }}</td>
                <td class="dt-left">{{ $u->telp ?? '-' }}</td>
                <td>{{ $u->alamat ?? '-' }}</td>
                <td class="dt-left">{{ $u->nip ?? '-' }}</td>
                <td class="dt-left">{{ $u->nik ?? '-' }}</td>
                <td>{{ $u->email ?? '-' }}</td>
                <td>{{ $u->role ?? '-' }}</td>
                <td>
                    @if ($u->status == '1')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Tidak Aktif</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $u->kd_karyawan }}" data-nama="{{ $u->nama }}" data-username="{{ $u->username }}"
                        data-telp="{{ $u->telp }}" data-alamat="{{ $u->alamat }}" data-nip="{{ $u->nip }}"
                        data-nik="{{ $u->nik }}" data-email="{{ $u->email }}" data-role="{{ $u->role }}"
                        data-password="{{ $u->password }}" data-status="{{ $u->status }}">
                        Edit
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                if ($('#TampilkanSemuaUser').is(':checked')) {
                    return true;
                }
                var status = $(settings.aoData[dataIndex].nTr).data('status');
                return status == '1';
            }
        );

        var table = $('#usersTable').DataTable({
            scrollX: true,
            paging: false,
            columnDefs: [{
                "searchable": false,
                "orderable": false,
                "targets": 0
            }]
        });

        table.on('draw.dt', function () {
            var PageInfo = table.page.info();
            table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        table.draw();

        $('#TampilkanSemuaUser').change(function () {
            table.draw();
        });
    });

    $('#usersTable').on('click', '.tombol-edit', function () {
        const kd_karyawan = $(this).data('id');
        const nama = $(this).data('nama');
        const username = $(this).data('username');
        const telp = $(this).data('telp');
        const alamat = $(this).data('alamat');
        const nip = $(this).data('nip');
        const nik = $(this).data('nik');
        const email = $(this).data('email');
        const role = $(this).data('role');
        const status = $(this).data('status');

        $('#edit-nama').val(nama);
        $('#edit-username').val(username);
        $('#edit-telp').val(telp);
        $('#edit-alamat').val(alamat);
        $('#edit-nip').val(nip);
        $('#edit-nik').val(nik);
        $('#edit-email').val(email);
        $('#edit-role').val(role);
        $('#edit-status').val(status);

        let form = $('#form-edit-user');
        let updateUrl = "{{ route('users.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', kd_karyawan);
        form.attr('action', updateUrl);
    })
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
    @if (session()->has('invalid'))
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
            text: '{{ session('invalid') }}',
        })
        @if ($errors->any() && session('invalid') == 'User gagal diubah.')
            const errorUserId = "{{ session('error_user_id') }}";

            if (errorUserId) {
                let form = $('#form-edit-user');
                let updateUrl = "{{ url('users') }}/" + errorUserId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @elseif ($errors->any() && session('invalid') == 'User gagal ditambahkan.')
            $('#modalTambah').modal('show');
        @endif
    @endif
</script>
@endsection