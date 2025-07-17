@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
<h1>Barang</h1>
@stop

@section('plugins.Sweetalert2', true)

@section('css')
<style>
    .barang-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* 4 columns on desktop */
        gap: 32px;
        justify-items: center;
    }

    @media (max-width: 1200px) {
        .barang-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .barang-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .barang-container {
            grid-template-columns: 1fr;
        }
    }

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

    .barang-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 24px 16px 20px 16px;
        width: 100%;
        max-width: 340px;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        min-height: 340px;
    }

    .barang-img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 18px;
        background: #f3f3f3;
    }

    .barang-img:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .info-box-content {
        width: 100%;
        text-align: center;
    }

    .info-box-text {
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 6px;
        color: #222;
    }

    .info-box-number {
        font-size: 1.1rem;
        color: #1a1a1a;
        font-weight: 500;
        margin-bottom: 0;
        display: block;
    }

    .dropdown {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        z-index: 10;
    }
</style>
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
            <input type="number" class="form-control" id="harga" name="harga" min="0" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" min="0" value="0" required>
        </div>
        <div class="mb-3">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <img id="preview" src="" alt="Image preview"
                style="max-width: 20%; display: block; padding: 5px;display:none;">
        </div>
        <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name
            onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />
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
            <div class="mb-3">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <img id="preview_edit" src="" alt="Image preview"
                    style="max-width: 20%; display: block; padding: 5px;display:none;">
            </div>
            <x-adminlte-input-file name="foto" id="edit_foto"
                label="Upload Foto Baru (Kosongkan jika tidak ingin ganti)" placeholder="Pilih file"
                onchange="document.getElementById('preview_edit').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview_edit').style.display = 'block';" />
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan Perubahan" type="submit" form="editBarangForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>
<x-adminlte-modal id="modalTambahStok" title="Tambahkan Stok" theme="success" icon="fas fa-plus-circle" size='md'>
    <form method="POST" id="stokFormTambah">
        @csrf
        @method('PUT')
        <x-adminlte-input name="tambah_stok" label="Jumlah yang Ditambahkan" type="number" min="1" required />
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Tambahkan" type="submit" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="secondary" />
        </x-slot>
    </form>
</x-adminlte-modal>
<x-adminlte-modal id="modalKurangiStok" title="Kurangi Stok" theme="danger" icon="fas fa-minus-circle" size='md'>
    <form method="POST" id="stokFormKurang">
        @csrf
        @method('PUT')
        <x-adminlte-input name="kurangi_stok" label="Jumlah yang Dikurangi" type="number" min="1" required />
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Kurangi" type="submit" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="secondary" />
        </x-slot>
    </form>
</x-adminlte-modal>
<div class="d-flex justify-content-end">
    <x-adminlte-button label="Tambahkan Barang" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
</div>
<hr>
<div class="barang-container">
    @forelse ($barang as $b)
        <div class="barang-card">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle no-arrow" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" style="color: #6c757d;">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item edit-btn" href="#" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $b->id }}" data-nama="{{ $b->nama_barang }}" data-harga="{{ $b->harga }}"
                        data-foto="{{ asset('storage/images/barang/' . $b->foto) }}"
                        data-url="{{ route('barang.update', $b->kd_barang) }}">
                        <i class="fas fa-edit fa-fw mr-2 text-info"></i>Edit
                    </a>
                    <a class="dropdown-item tambah-stok-btn" href="#" data-id="{{ $b->kd_barang }}">
                        <i class="fas fa-arrow-up fa-fw mr-2 text-success"></i>Tambah Stok
                    </a>
                    <a class="dropdown-item kurangi-stok-btn" href="#" data-id="{{ $b->kd_barang }}">
                        <i class="fas fa-arrow-down fa-fw mr-2 text-danger"></i>Kurangi Stok
                    </a>
                    <form action="{{ route('barang.destroy', $b->kd_barang) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item btn-danger tombol-hapus">
                            <i class="fas fa-trash fa-fw mr-2 text-danger"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
            <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                data-src="{{ asset('storage/images/barang/' . $b->foto) }}">
                <img class="barang-img" src="{{ asset('storage/images/barang/' . $b->foto) }}" alt="Foto Barang">
            </a>
            <div class="info-box-content">
                <span class="info-box-text">{{ $b->nama_barang }}</span>
                <span class="info-box-number">{{ 'Rp' . number_format($b->harga, 2, ',', '.') }}</span>
            </div>
            <div class="stok-info">
                Stok: {{ $b->stok }}
            </div>
        </div>
    @empty
        <div class="col-md-12">
            <p class="text-center text-muted">Belum ada barang yang ditambahkan.</p>
        </div>
    @endforelse
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid" alt="Barang Image">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        function setupFormAction(button, modalId, formId) {
            const id = $(button).data('id');
            const updateUrl = "{{ url('barang') }}/" + id + "/updateStok";
            $(formId).attr('action', updateUrl);
            $(modalId).modal('show');
        }
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });

        $('.barang-container').on('click', '.tambah-stok-btn', function (e) {
            e.preventDefault();
            setupFormAction(this, '#modalTambahStok', '#stokFormTambah');
        });

        $('.barang-container').on('click', '.kurangi-stok-btn', function (e) {
            e.preventDefault();
            setupFormAction(this, '#modalKurangiStok', '#stokFormKurang');
        });

        $('.barang-container').on('click', '.edit-btn', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const harga = $(this).data('harga');
            const fotoUrl = $(this).data('foto');
            const updateUrl = "{{ url('barang') }}/" + id;

            $('#edit_nama_barang').val(nama);
            $('#edit_harga').val(harga);
            $('#current_foto_preview').attr('src', fotoUrl);
            $('#editBarangForm').attr('action', updateUrl);

            $('#modalEdit').modal('show');
        });
    });
    $(document).on('click', '.tombol-hapus', function (e) {
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