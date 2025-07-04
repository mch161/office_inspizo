@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
    <h1>Barang</h1>
@stop

@section('plugins.Sweetalert2', true)

@section('css')
    <style>
        .dropdown-toggle.no-arrow::after {
            display: none;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var nama = $(this).data('nama');
                var harga = $(this).data('harga');
                var fotoUrl = $(this).data('foto');
                var updateUrl = $(this).data('url');

                $('#edit_nama_barang').val(nama);
                $('#edit_harga').val(harga);
                $('#current_foto_preview').attr('src', fotoUrl);

                $('#editBarangForm').attr('action', updateUrl);
            });
        });
        $(document).on('click', '.tombol-hapus', function(e) {
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
@endsection

@section('content')
    <x-adminlte-modal id="modalTambah" title="Tambahkan Barang" theme="success" icon="fas fa-box" size='lg'>
        <form action="{{ route('barang.store') }}" method="POST" id="barangForm" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" required>
            </div>
            <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name />

            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="barangForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    <x-adminlte-modal id="modalEdit" title="Edit Barang" theme="warning" icon="fas fa-edit" size='lg'>
        <form method="POST" id="editBarangForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_nama_barang">Nama Barang</label>
                <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
            </div>
            <div class="form-group">
                <label for="edit_harga">Harga</label>
                <input type="number" class="form-control" id="edit_harga" name="harga" required>
            </div>
            <div class="form-group">
                <label>Foto Saat Ini</label>
                <div>
                    <img id="current_foto_preview" src="" alt="Foto Saat Ini"
                        style="max-width: 200px; max-height: 200px; margin-bottom: 10px;">
                </div>
                <x-adminlte-input-file name="foto" id="edit_foto"
                    label="Upload Foto Baru (Kosongkan jika tidak ingin ganti)" placeholder="Pilih file" />

            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan Perubahan" type="submit" form="editBarangForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    <div class="d-flex justify-content-end">
        <x-adminlte-button label="Tambahkan Barang" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
    </div>
    <hr>
    <div class="container">
        @foreach ($barang as $b)
            <div class="col-md-4">
                <div class="info-box" style="position: relative;">
                    <div class="dropdown" style="position: absolute; top: 0.5rem; right: 0.5rem; z-index: 10;">
                        <button class="btn btn-link dropdown-toggle no-arrow" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" style="color: #6c757d;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu">
                            <a class="dropdown-item edit-btn" href="#" data-toggle="modal" data-target="#modalEdit"
                                data-id="{{ $b->id }}" data-nama="{{ $b->nama_barang }}"
                                data-harga="{{ $b->harga }}"
                                data-foto="{{ asset('storage/images/barang/' . $b->foto) }}"
                                data-url="{{ route('barang.update', $b->kd_barang) }}">
                                Edit
                            </a>
                            <form action="{{ route('barang.destroy', $b->kd_barang) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item btn-danger tombol-hapus">Delete</button>
                            </form>
                        </div>
                    </div>

                    <img src="{{ asset('storage/images/barang/' . $b->foto) }}" alt="{{ $b->nama_barang }}"
                        width="50%">
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $b->nama_barang }}</span>
                        <span class="info-box-number">{{ 'Rp' . number_format($b->harga, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
