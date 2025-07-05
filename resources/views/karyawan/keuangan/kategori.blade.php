@extends('adminlte::page')

@section('title', 'Keuangan | Kotak')

@section('content_header')
<h1>Kotak</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
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

<table id="KotakTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kotak</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kotak as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $k->nama }}</td>
                <td>
                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $k->kd_kotak }}" data-nama_kotak="{{ $k->nama_kotak }}">
                        Edit
                    </button>
                    <form action="{{ route('kotak.destroy', $k->kd_kotak) }}" method="POST" style="display:inline;">
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
        $('#KotakTable').DataTable();
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
@endsection