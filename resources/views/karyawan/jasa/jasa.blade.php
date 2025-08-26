@extends('adminlte::page')

@section('title', 'Jasa')

@section('content_header')
<h1>Jasa</h1>
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
<div class="row">
    <div class="col-12">
        <x-adminlte-modal id="modalTambah" title="Tambah Jasa" theme="primary">
            <form action="{{ route('jasa.store') }}" method="POST" id="jasaForm">
                @csrf
                <div class="form-group">
                    <label for="nama_jasa">Nama Jasa</label>
                    <input type="text" class="form-control" id="nama_jasa" name="nama_jasa" placeholder="Masukkan Nama"
                        required>
                </div>
                <div class="form-group">
                    <label for="tarif">Tarif</label>
                    <input type="number" class="form-control" id="tarif" name="tarif" placeholder="Masukkan Tarif"
                        required>
                </div>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="primary" label="Simpan" type="submit" form="jasaForm" />
                    <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
                </x-slot>
            </form>
        </x-adminlte-modal>
        <x-adminlte-modal id="modalEdit" title="Edit Jasa" theme="warning">
            <form action="" method="POST" id="jasaEditForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama_jasa">Nama Jasa</label>
                    <input type="text" class="form-control" id="nama_jasa" name="nama_jasa" placeholder="Masukkan Nama"
                        required>
                </div>
                <div class="form-group">
                    <label for="tarif">Tarif</label>
                    <input type="number" class="form-control" id="tarif" name="tarif" placeholder="Masukkan Tarif"
                        required>
                </div>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="primary" label="Simpan" type="submit" form="jasaEditForm" />
                    <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
                </x-slot>
            </form>
        </x-adminlte-modal>

        <div class="card">
            <div class="card-body">
                <x-adminlte-button data-toggle="modal" data-target="#modalTambah" label="Tambah Jasa" theme="primary"
                    icon="fas fa-plus" class="mb-3 float-right" />
                <table id="jasaTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="50px">No</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jasa as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_jasa }}</td>
                                <td>Rp{{ number_format($item->tarif, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal"
                                        data-target="#modalEdit" data-id="{{ $item->kd_jasa }}"
                                        data-nama="{{ $item->nama_jasa }}" data-tarif="{{ $item->tarif }}"><i
                                            class="fas fa-edit"></i>Edit</button>
                                    <form action="{{ route('jasa.destroy', $item->kd_jasa) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                                            <i class="fas fa-trash"></i>
                                            Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#jasaTable').DataTable({
            responsive: true,
            lengthChange: false,
            pageLength: -1,
            scrollX: true,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data.",
                emptyTable: "Tidak ada data jasa yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                searchPlaceholder: "Cari data..."
            }
        });
    })
    $(document).on('click', '.tombol-edit', function () {
        const kd_jasa = $(this).data('id');
        const nama_jasa = $(this).data('nama');
        const tarif = $(this).data('tarif');
        $('#modalEdit #kd_jasa').val(kd_jasa);
        $('#modalEdit #nama_jasa').val(nama_jasa);
        $('#modalEdit #tarif').val(tarif);

        let form = $('#jasaEditForm');
        let updateUrl = "{{ url('jasa') }}/" + kd_jasa;
        form.attr('action', updateUrl);
    })
    $(document).on('click', '.tombol-hapus', function (e) {
        e.preventDefault();
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
                $(this).closest('form').submit();
            }
        })
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
</script>
@stop