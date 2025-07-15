@extends('adminlte::page')

@section('title', 'Keuangan | Kotak')

@section('content_header')
<h1>Kotak</h1>
@stop

@section('css')

@endsection

@section('content')
<div class="d-flex justify-content-end">
    <x-adminlte-button label="Tambahkan Kotak" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
</div>

<x-adminlte-modal id="modalTambah" title="Tambahkan Kotak" theme="success" icon="fas fa-box" size='lg'>
    <form action="{{ route('kotak.store') }}" method="POST" id="kotakForm" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="nama_kotak">Nama Kotak</label>
            <input type="text" class="form-control" id="nama_kotak" name="nama" required>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="kotakForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="modalEdit" title="Edit Kotak" theme="primary" icon="fas fa-edit" size='lg'>
    <form method="POST" id="form-edit-kotak" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama_kotak_edit">Nama Kotak</label>
            <input type="text" class="form-control" id="nama_kotak_edit" name="nama" required>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Simpan Perubahan" type="submit" form="form-edit-kotak" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<table id="KotakTable" class="table table-bordered table-striped">
    <thead>
        <tr class="table-primary">
            <th width="5%">No</th>
            <th>Nama Kotak</th>
            <th width="150px" rigth>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kotak as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $k->nama }}</td>
                <td>
                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $k->kd_kotak }}" data-nama_kotak="{{ $k->nama }}">
                        Edit
                    </button>
                    <form action="{{ route('kotak.destroy', $k->kd_kotak) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm tombol-hapus">Hapus</button>
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
        $('#KotakTable').DataTable({
            scrollX: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '200px'
        });

        $('.tombol-edit').on('click', function () {
            const id = $(this).data('id');
            const nama_kotak = $(this).data('nama_kotak');

            $('#nama_kotak_edit').val(nama_kotak);

            let form = $('#form-edit-kotak');
            let updateUrl = "{{ url('kotak') }}/" + id;
            form.attr('action', updateUrl);
        });

        @if ($errors->any() && session('invalid_kotak'))
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
                text: '{{ session('invalid_kotak') }}',
            })
            const errorkotakId = "{{ session('error_kotak_id') }}";

            if (errorkotakId) {
                let form = $('#form-edit-kotak');
                let updateUrl = "{{ url('kotak') }}/" + errorkotakId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @endif

        $('#KotakTable').on('click', '.tombol-hapus', function (e) {
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
