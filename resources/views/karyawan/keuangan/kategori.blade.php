@extends('adminlte::page')

@section('title', 'Keuangan | Kategori')

@section('content_header')
<h1>Kategori</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('content')
<div class="d-flex justify-content-end">
    <x-adminlte-button label="Tambahkan kategori" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
</div>

<x-adminlte-modal id="modalTambah" title="Tambahkan kategori" theme="success" icon="fas fa-box" size='lg'>
    <form action="{{ route('kategori.store') }}" method="POST" id="kategoriForm" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="nama_kategori">Nama kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama" required>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="kategoriForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="modalEdit" title="Edit Kategori" theme="primary" icon="fas fa-edit" size='lg'>
    <form method="POST" id="form-edit-kategori" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama_kategori_edit">Nama kategori</label>
            <input type="text" class="form-control" id="nama_kategori_edit" name="nama" required>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Simpan Perubahan" type="submit" form="form-edit-kategori" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<table id="kategoriTable" class="table table-bordered table-striped">
    <thead>
        <tr class="table-primary">
            <th width="5%">No</th>
            <th>Nama Kategori</th>
            <th width="150px" rigth>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kategori as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $k->nama }}</td>
                <td>
                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $k->kd_kategori }}" data-nama_kategori="{{ $k->nama }}">
                        Edit
                    </button>
                    <form action="{{ route('kategori.destroy', $k->kd_kategori) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm tombol-hapus">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#kategoriTable').DataTable({
            scrollX: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '200px'
        });

        $('.tombol-edit').on('click', function () {
            const id = $(this).data('id');
            const nama_kategori = $(this).data('nama_kategori');

            $('#nama_kategori_edit').val(nama_kategori);

            let form = $('#form-edit-kategori');
            let updateUrl = "{{ url('kategori') }}/" + id;
            form.attr('action', updateUrl);
        });

        @if ($errors->any() && session('invalid_kategori'))
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
                text: '{{ session('invalid_kategori') }}',
            })
            const errorkategoriId = "{{ session('error_kategori_id') }}";

            if (errorkategoriId) {
                let form = $('#form-edit-kategori');
                let updateUrl = "{{ url('kategori') }}/" + errorkategoriId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @endif

        $('#kategoriTable').on('click', '.tombol-hapus', function (e) {
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
    });
</script>
@endsection
