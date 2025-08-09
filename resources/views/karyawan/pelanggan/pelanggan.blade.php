@extends('adminlte::page')

@section('title', 'Pelanggan')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1><i class="fas fa-users"></i> Pelanggan</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-body">
        <x-adminlte-button label="Tambahkan Pelanggan" class="float-right mb-2 bg-blue" data-toggle="modal"
            data-target="#tambahModal" />
        <table id="pelangganTable" class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Alamat</th>
                    <th>No Telepon</th>
                    <th>NIK</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th width="150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelanggan as $pelanggan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pelanggan->nama_pelanggan }}</td>
                        <td>{{ $pelanggan->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $pelanggan->alamat_pelanggan ?? '-' }}</td>
                        <td>{{ $pelanggan->telp_pelanggan ?? '-' }}</td>
                        <td>{{ $pelanggan->nik ?? '-' }}</td>
                        <td>{{ $pelanggan->username ?? '-' }}</td>
                        <td>{{ $pelanggan->email ?? '-' }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#editModal"
                                data-id="{{ $pelanggan->kd_pelanggan }}"
                                data-nama_pelanggan="{{ $pelanggan->nama_pelanggan }}"
                                data-nama_perusahaan="{{ $pelanggan->nama_perusahaan }}"
                                data-alamat_pelanggan="{{ $pelanggan->alamat_pelanggan }}"
                                data-telp_pelanggan="{{ $pelanggan->telp_pelanggan }}" data-nik="{{ $pelanggan->nik }}"
                                data-username="{{ $pelanggan->username }}" data-email="{{ $pelanggan->email }}"><i
                                    class="fas fa-edit"></i> Edit
                            </button>

                            <form action="{{ route('pelanggan.destroy', $pelanggan->kd_pelanggan) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm tombol-hapus">Hapus</button>
                            </form>
                        </td>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<x-adminlte-modal id="editModal" title="Edit Pelanggan" theme="success" icon="fas fa-users" size='lg'>
    <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="kd_pelanggan_edit" id="kd_pelanggan_edit">
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-input id="nama_pelanggan_edit" name="nama_pelanggan_edit" label="Nama Pelanggan"
                    placeholder="Masukkan Nama" required autocomplete="off" />
                <x-adminlte-input id="nama_perusahaan_edit" name="nama_perusahaan_edit" label="Nama Perusahaan"
                    placeholder="Masukkan Nama Perusahaan" autocomplete="off" />
                <x-adminlte-textarea id="alamat_pelanggan_edit" name="alamat_pelanggan_edit" label="Alamat"
                    placeholder="Masukkan Alamat" rows="4" autocomplete="off" />
                <x-adminlte-input id="nik_edit" name="nik_edit" label="NIK" placeholder="Masukkan NIK"
                    autocomplete="off" />

            </div>
            <div class="col-md-6">
                <x-adminlte-input id="username_edit" name="username_edit" label="Username"
                    placeholder="Masukkan Username" autocomplete="off" />
                <x-adminlte-input id="telp_pelanggan_edit" name="telp_pelanggan_edit" label="Nomor Telepon"
                    placeholder="Masukkan Nomor Telepon" autocomplete="off" />
                <x-adminlte-input id="email_edit" type="email" name="email_edit" label="Email"
                    placeholder="Masukkan Email" autocomplete="off" />
                <x-adminlte-input id="password_edit" type="password" name="password_edit" label="Password"
                    placeholder="Biarkan kosong jika tidak ingin mengganti" autocomplete="new-password" />
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Simpan Perubahan" type="submit" form="editForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="tambahModal" title="Tambahkan Pelanggan" theme="success" icon="fas fa-users" size='lg'>
    <form id="tambahForm" action="{{ route('pelanggan.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-input name="nama_pelanggan" label="Nama Pelanggan" placeholder="Masukkan Nama" required
                    autocomplete="off" />
                <x-adminlte-input name="nama_perusahaan" label="Nama Perusahaan" placeholder="Masukkan Nama Perusahaan"
                    autocomplete="off" />
                <x-adminlte-textarea name="alamat_pelanggan" label="Alamat" placeholder="Masukkan Alamat" rows="4"
                    autocomplete="off" />
                <x-adminlte-input name="nik" label="NIK" placeholder="Masukkan NIK" autocomplete="off" />

            </div>
            <div class="col-md-6">
                <x-adminlte-input name="username" label="Username" placeholder="Masukkan Username"
                    autocomplete="off" />
                <x-adminlte-input name="telp_pelanggan" label="Nomor Telepon" placeholder="Masukkan Nomor Telepon"
                    autocomplete="off" />
                <x-adminlte-input type="email" name="email" label="Email" placeholder="Masukkan Email" 
                    autocomplete="off" />
                <x-adminlte-input type="password" name="password" label="Password" placeholder="Masukkan Password"
                    autocomplete="new-password" />
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Simpan" type="submit" form="tambahForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#pelangganTable').DataTable({
            scrollX: true,
            scrollCollapse: true,
            pageLength: 10,
            lengthChange: false,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                searchPlaceholder: "Cari data..."
            }
        });
        $('.tombol-edit').on('click', function () {
            var id = $(this).data('id');
            var nama_pelanggan = $(this).data('nama_pelanggan');
            var nama_perusahaan = $(this).data('nama_perusahaan');
            var alamat_pelanggan = $(this).data('alamat_pelanggan');
            var nik = $(this).data('nik');
            var username = $(this).data('username');
            var telp_pelanggan = $(this).data('telp_pelanggan');
            var email = $(this).data('email');

            $('#nama_pelanggan_edit').val(nama_pelanggan);
            $('#nama_perusahaan_edit').val(nama_perusahaan);
            $('#alamat_pelanggan_edit').val(alamat_pelanggan);
            $('#nik_edit').val(nik);
            $('#username_edit').val(username);
            $('#telp_pelanggan_edit').val(telp_pelanggan);
            $('#email_edit').val(email);

            let form = $('#editForm');
            let updateUrl = "{{ url('pelanggan') }}/" + id;
            form.attr('action', updateUrl);
        });
        $('#pelangganTable').on('click', '.tombol-hapus', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
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