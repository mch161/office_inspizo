@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
<h1>Barang</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

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

        /* Fix for dropdown menu on the right edge */
        .barang-card .dropdown-menu {
            /* Ensures the menu aligns with the button */
            transform: translateX(-85%);
        }

        /* For 4 columns on desktop */
        .barang-container .barang-card:nth-child(4n) .dropdown-menu {
            left: auto;
            right: 0;
            transform: translateX(0);
        }

        /* For 3 columns on tablets */
        @media (max-width: 1200px) {
            .barang-container .barang-card:nth-child(4n) .dropdown-menu {
                left: 0;
                right: auto;
                transform: translateX(-85%);
            }

            .barang-container .barang-card:nth-child(3n) .dropdown-menu {
                left: auto;
                right: 0;
                transform: translateX(0);
            }
        }

        /* For 2 columns on small tablets */
        @media (max-width: 900px) {
            .barang-container .barang-card:nth-child(3n) .dropdown-menu {
                left: 0;
                right: auto;
                transform: translateX(-85%);
            }

            .barang-container .barang-card:nth-child(2n) .dropdown-menu {
                left: auto;
                right: 0;
                transform: translateX(0);
            }
        }

        /* For 1 column on mobile, reset all */
        @media (max-width: 600px) {

            .barang-container .barang-card:nth-child(2n) .dropdown-menu,
            .barang-container .barang-card:nth-child(3n) .dropdown-menu {
                left: 0;
                right: auto;
                transform: translateX(-85%);
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
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                        placeholder="Masukkan Nama Barang" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="Dijual">Dijual?</label>
                    <select name="dijual" id="dijual" class="form-control">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="klasifikasi">Klasifikasi</label>
                    <select name="klasifikasi" id="klasifikasi" class="form-control">
                        <option value="Baru">Baru</option>
                        <option value="Second">Second</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="Barcode">Barcode</label>
                    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Masukkan Barcode">
                </div>
                <div class="col-md-12">
                    @php
                        $config = [
                            "placeholder" => "Cari atau ketik kode barang baru...",
                            "allowClear" => true,
                            "tags" => true,
                            "dropdownParent" => "#modalTambah",
                        ];
                    @endphp
                    <x-adminlte-select2 name="kode" label="Kode" :config="$config" id="kode">
                        <option value="" disabled selected>Pilih Kode Barang</option>
                        @foreach ($barang->unique('kode')->sortBy('kode') as $data)
                            <option value="{{ $data->kode }}">{{ $data->kode }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="form-group col-md-4">
                    <label for="hpp">HPP</label>
                    <input type="number" class="form-control" id="hpp" name="hpp" min="0" placeholder="10000">
                </div>
                <div class="form-group col-md-4">
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" min="0" placeholder="10000">
                </div>
                <div class="form-group col-md-4">
                    <label for="stok">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" min="0" placeholder="0">
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
                <div class="form-group col-md-12">
                    <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name
                        onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />
                </div>
            </div>
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
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="edit_nama_barang">Nama Barang</label>
                    <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="edit_dijual">Dijual?</label>
                    <select name="dijual" id="edit_dijual" class="form-control">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="edit_klasifikasi">Klasifikasi</label>
                    <select name="klasifikasi" id="edit_klasifikasi" class="form-control">
                        <option value="Baru">Baru</option>
                        <option value="Second">Second</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="edit_barcode">Barcode</label>
                    <input type="text" class="form-control" id="edit_barcode" name="barcode">
                </div>
                <div class="col-md-12">
                    @php
                        $config2 = [
                            "placeholder" => "Cari atau ketik kode barang baru...",
                            "allowClear" => true,
                            "tags" => true,
                            "dropdownParent" => "#modalEdit",
                        ];
                    @endphp
                    <x-adminlte-select2 name="edit_kode" label="Kode" :config="$config2" id="kode2">
                        <option value="" disabled selected>Pilih Kode Barang"></option>
                        @foreach ($barang->unique('kode')->sortBy('kode') as $data)
                            <option value="{{ $data->kode }}">{{ $data->kode }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="form-group col-md-6">
                    <label for="edit_hpp">HPP</label>
                    <input type="number" class="form-control" id="edit_hpp" name="hpp">
                </div>
                <div class="form-group col-md-6">
                    <label for="edit_harga">Harga</label>
                    <input type="number" class="form-control" id="edit_harga" name="harga">
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
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan Perubahan" type="submit" form="editBarangForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>

    <x-adminlte-modal id="modalTambahStok" title="Tambahkan Stok" theme="success" icon="fas fa-plus-circle" size='md'>
        <form action="{{ route('stok.store') }}" method="POST" id="stokFormTambah">
            @csrf
            <input type="hidden" name="kd_barang" id="tambah_kd_barang">
            <input type="hidden" name="klasifikasi" value="Stok Masuk">
            <x-adminlte-input name="jumlah" label="Jumlah yang Ditambahkan" type="number" min="1" required />
            <x-adminlte-textarea name="keterangan" label="Keterangan (Opsional)"
                placeholder="Contoh: Pembelian dari supplier A" />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Tambahkan" type="submit" form="stokFormTambah" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="secondary" />
            </x-slot>
        </form>
    </x-adminlte-modal>

    <x-adminlte-modal id="modalKurangiStok" title="Kurangi Stok" theme="danger" icon="fas fa-minus-circle" size='md'>
        <form action="{{ route('stok.store') }}" method="POST" id="stokFormKurang">
            @csrf
            <input type="hidden" name="kd_barang" id="kurang_kd_barang">
            <input type="hidden" name="klasifikasi" value="Stok Keluar">
            <x-adminlte-input name="jumlah" label="Jumlah yang Dikurangi" type="number" min="1" required />
            <x-adminlte-textarea name="keterangan" label="Keterangan (Opsional)"
                placeholder="Contoh: Penjualan kepada pelanggan B" />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="danger" label="Kurangi" type="submit" form="stokFormKurang" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="secondary" />
            </x-slot>
        </form>
    </x-adminlte-modal>

    <div class="d-flex justify-content-end">
        <x-adminlte-button label="Tambahkan Barang" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
    </div>
    <hr>
    <form action="{{ route('barang.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="s" class="form-control" placeholder="Cari nama barang..." value="{{ request('s') }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </div>
    </form>
    <div class="mb-4 text-center">
        <button class="btn btn-outline-primary btn-filter active" data-filter="all">Semua</button>
        @foreach($barang->unique('kode')->sortBy('kode')->pluck('kode') as $kode)
            <button class="btn btn-outline-primary btn-filter" data-filter="{{ $kode }}">{{ $kode }}</button>
        @endforeach
    </div>
    <div class="barang-container">
        @forelse ($barang as $b)
            <div class="barang-card" data-kode="{{ $b->kode }}">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle no-arrow" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" style="color: #6c757d;">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item edit-btn" href="#" data-toggle="modal" data-target="#modalEdit"
                            data-id="{{ $b->id }}" data-kode="{{ $b->kode }}" data-nama="{{ $b->nama_barang }}"
                            data-dijual="{{ $b->dijual }}" data-klasifikasi="{{ $b->klasifikasi }}"
                            data-barcode="{{ $b->barcode }}" data-hpp="{{ $b->hpp }}" data-harga="{{ $b->harga_jual }}"
                            data-foto="{{ asset('storage/images/barang/' . $b->foto) }}"
                            data-url="{{ route('barang.update', $b->kd_barang) }}">
                            <i class="fas fa-edit fa-fw mr-2 text-info"></i>Edit
                        </a>
                        <a class="dropdown-item tambah-stok-btn" href="#" data-id="{{ $b->kd_barang }}">
                            <i class="fas fa-plus-circle fa-fw mr-2 text-success"></i>Tambah Stok
                        </a>
                        <a class="dropdown-item kurangi-stok-btn" href="#" data-id="{{ $b->kd_barang }}">
                            <i class="fas fa-minus-circle fa-fw mr-2 text-danger"></i>Kurangi Stok
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
                @if ($b->foto)
                    <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                        data-src="{{ asset('storage/images/barang/' . $b->foto) }}">
                        <img class="barang-img" src="{{ asset('storage/images/barang/' . $b->foto) }}" alt="Foto Barang">
                    </a>
                @else
                    <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                @endif
                <div class="info-box-content">
                    <span class="info-box-text">{{ $b->nama_barang }} ({{ $b->kode }})</span>
                    <span class="info-box-number">Rp{{ number_format($b->harga_jual, 2, ',', '.') }}</span>
                </div>
                <div class="stok-info">
                    Stok: {{ $b->stok }}
                </div>
            </div>
        @empty
            <div class="col-md">
                @if (request('s'))
                    <div class="text-muted text-center">
                        <p>Tidak ada data barang yang sesuai dengan pencarian "{{ request('s') }}".</p>
                    </div>
                @else
                    <div class="text-muted text-center">
                        <p>Saat ini belum ada data barang yang tersedia di dalam sistem.</p>
                    </div>
                @endif
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
            $('.btn-filter').on('click', function () {
                $('.btn-filter').removeClass('active');
                $(this).addClass('active');

                const filterValue = $(this).data('filter');

                if (filterValue === 'all') {
                    $('.barang-card').fadeIn();
                } else {
                    $('.barang-card').fadeOut('fast');
                    $('.barang-card[data-kode="' + filterValue + '"]').fadeIn();
                }
            });
        });
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        $(document).ready(function () {
            $('.barang-container').on('click', '.tambah-stok-btn', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                $('#tambah_kd_barang').val(id);
                $('#modalTambahStok').modal('show');
            });

            $('.barang-container').on('click', '.kurangi-stok-btn', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                $('#kurang_kd_barang').val(id);
                $('#modalKurangiStok').modal('show');
            });

            $('.image-popup').on('click', function (e) {
                e.preventDefault();
                const imageUrl = $(this).data('src');
                $('#modalImage').attr('src', imageUrl);
            });
            $('.barang-container').on('click', '.edit-btn', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const dijual = $(this).data('dijual');
                const klasifikasi = $(this).data('klasifikasi');
                const barcode = $(this).data('barcode');
                const kode = $(this).data('kode');
                const hpp = $(this).data('hpp');
                const harga = $(this).data('harga');
                const fotoUrl = $(this).data('foto');
                const updateUrl = $(this).data('url');

                $('#edit_nama_barang').val(nama);

                $('#edit_dijual').val(dijual);
                $('#edit_dijual').trigger('change');
                $('#edit_klasifikasi').val(klasifikasi);
                $('#edit_klasifikasi').trigger('change');
                $('#kode2').val(kode);
                $('#kode2').trigger('change');

                $('#edit_barcode').val(barcode);
                $('#edit_hpp').val(hpp);
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