@extends('adminlte::page')

@section('title', 'Galeri ' . ucfirst($type))

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.KrajeeFileinput', true)

@section('content_header')
<h1><i class="fas fa-images"></i> Galeri {{ ucfirst($type) }} #{{ $item->getKey() }}</h1>
@stop

@section('css')
    <style>
        .galeri-image {
            width: auto;
            height: 200px;
            border-radius: .5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-image {
            width: auto;
            height: 200px;
            border-radius: .5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-image:hover {
            filter: brightness(0.7);
        }

        .galeri-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        #modalImage {
            width: auto;
            max-height: 90vh;
            max-width: 100%;
        }

        #imageModal .modal-content {
            width: fit-content;
            height: fit-content;
            border: none;
            padding: 0;
            position: absolute;
        }

        #imageModal .modal-body {
            padding: 0;
        }

        .alert {
            animation: fadeIn 0.5s;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools float-end">
                {{-- Tombol Kembali Dinamis --}}
                @php
                    $back_route = ($type === 'pesanan') ? 'pesanan.detail' : 'pekerjaan.show'; 
                    $back_param = ($type === 'pesanan') ? 'pesanan' : 'pekerjaan';
                @endphp
                <a href="{{ route($back_route, [$back_param => $item->getKey()]) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Detail {{ ucfirst($type) }}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($is_combined_view) && $is_combined_view)
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> Tampilan ini menggabungkan foto dari Pesanan ini dan Pekerjaan terkait.
            </div>
            @endif
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#uploadModal"><i
                        class="fas fa-upload"></i>
                    Upload</button>
                <button class="btn btn-danger" id="togglHapus"><i class="fas fa-trash"></i> Hapus</button>
                <button class="btn btn-info d-none" id="togglBatal"><i class="fas fa-times"></i> Batal</button>
            </div>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> Klik pada gambar untuk melihat.
            </div>
            <div class="alert alert-danger d-none" role="alert">
                <i class="fas fa-exclamation-triangle"></i> Klik pada gambar untuk menghapus.
            </div>
        </div>
    </div>

    <hr>

    <x-adminlte-modal id="uploadModal" title="Upload Galeri">
        {{-- Rute store dinamis: Menggunakan ID item dan type --}}
        <form action="{{ route('galeri.store', ['type' => $type, 'id' => $item->getKey()]) }}" 
              method="POST" 
              enctype="multipart/form-data">
            @csrf
            <x-adminlte-input-file-krajee id="kifPholder" name="foto[]" igroup-size="sm"
                data-msg-placeholder="Choose multiple files..." data-show-cancel="false" data-show-close="false" multiple
                required />
            <button type="submit" class="btn btn-primary mt-3">Upload Sekarang</button>
        </form>
        <x-slot name="footerSlot">
            <x-adminlte-button label="Tutup" data-dismiss="modal" theme="danger" />
        </x-slot>
    </x-adminlte-modal>

    <div class="row">
        @forelse ($galeri as $galeri_item)
            @php
                // Logika Penentuan Path:
                // Tentukan folder dan ID dari Model yang sebenarnya
                $is_pesanan_galeri = ($galeri_item instanceof \App\Models\PesananGaleri);
                
                if ($is_pesanan_galeri) {
                    $folder_name = 'pesanan';
                    $folder_id = $galeri_item->kd_pesanan; 
                    $delete_type = 'pesanan';
                    $delete_id = $galeri_item->kd_pesanan;
                } else {
                    // Berarti ini PekerjaanGaleri
                    $folder_name = 'pekerjaan';
                    $folder_id = $galeri_item->kd_pekerjaan;
                    $delete_type = 'pekerjaan';
                    $delete_id = $galeri_item->kd_pekerjaan;
                }
                
                // Jika view-nya adalah Pesanan (gabungan), kita harus menggunakan ID dan type item aslinya untuk rute delete
                // agar redirect kembali ke halaman yang benar.
                if (isset($is_combined_view) && $is_combined_view) {
                    $delete_route_id = $item->getKey();
                    $delete_route_type = $type;
                } else {
                    // Jika view Pekerjaan, hapus harus menggunakan ID pekerjaan itu sendiri
                    $delete_route_id = $delete_id;
                    $delete_route_type = $delete_type;
                }
                
                $imagePath = 'storage/images/' . $folder_name . '/' . $folder_id . '/' . $galeri_item->foto;
            @endphp
            
            {{-- Tautan Popup --}}
            <a class="image-popup" data-toggle="modal" data-target="#imageModal"
                data-src="{{ asset($imagePath) }}">
                <img class="galeri-image m-1" src="{{ asset($imagePath) }}">
            </a>
            
            {{-- Form Hapus Dinamis --}}
            {{-- Catatan: Rute delete harus menggunakan type dan id yang sesuai dengan lokasi data yang akan dihapus, 
                TETAPI redirect harus kembali ke $type/$item->getKey(). 
                Untuk kesederhanaan, kita akan biarkan controller yang menangani redirect dengan benar. --}}
            
            <form action="{{ route('galeri.destroy', ['type' => $delete_type, 'id' => $delete_id, 'kd_galeri' => $galeri_item->kd_galeri]) }}" method="POST">
                @csrf
                @method('DELETE')
                <img class="delete-image m-1 d-none" src="{{ asset($imagePath) }}">
                {{-- Tambahkan field tersembunyi untuk redirect --}}
                <input type="hidden" name="redirect_type" value="{{ $type }}">
                <input type="hidden" name="redirect_id" value="{{ $item->getKey() }}">
            </form>
        @empty
            <div class="d-flex justify-content-center align-items-center flex-column m-auto">
                <i class="fas fa-camera-retro fa-5x text-muted mb-3"></i>
                <p class="text-center">Belum ada gambar untuk {{ ucfirst($type) }} ini.</p>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                    <i class="fas fa-upload"></i> Mulai Upload
                </button>
            </div>
        @endforelse
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"
            style="display: flex; align-items: center; justify-content: center;">
            <div class="modal-content">
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });
        $('#togglHapus').on('click', function (e) {
            $('#togglHapus').addClass('d-none');
            $('#togglBatal').removeClass('d-none');
            $('.galeri-image').addClass('d-none');
            $('.delete-image').removeClass('d-none');
            $('.alert-info').addClass('d-none');
            $('.alert-danger').removeClass('d-none');
        });
        $('#togglBatal').on('click', function (e) {
            $('#togglHapus').removeClass('d-none');
            $('#togglBatal').addClass('d-none');
            $('.galeri-image').removeClass('d-none');
            $('.delete-image').addClass('d-none');
            $('.alert-info').removeClass('d-none');
            $('.alert-danger').addClass('d-none');
        })
        $('.delete-image').on('click', function (e) {
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
                    escapeMarkup: false,
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