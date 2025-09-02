@extends('adminlte::page')

@section('title', 'Reimburse')

@section('content_header')
<h1>Table Reimburse</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('css')
    <style>
        .card {
            border-radius: .5rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        #ReimburseTable th,
        #ReimburseTable td {
            vertical-align: middle !important;
            text-align: center;
        }

        #ReimburseTable .keterangan-column {
            text-align: left;
            max-width: 300px;
            white-space: normal;
        }

        .reimburse-image {
            width: 150px;
            height: auto;
            border-radius: .5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .reimburse-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-sm i {
            margin-right: .25rem;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-3 float-right">
            <a href="{{ route('reimburse.form') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buat Pengajuan Reimburse
            </a>
        </div>
        <table id="ReimburseTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="5%" class="text-left">ID</th>
                    <th class="text-left">Nominal</th>
                    <th class="text-left">Foto</th>
                    <th class="text-left">Karyawan</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-left">Kategori</th>
                    <th class="text-left">Tanggal</th>
                    <th width="10%" class="text-left">Status</th>
                    <th class="text-left">Bukti Transfer</th>
                    @if (Auth::user()->role == 'superadmin')
                        <th class="text-left">Kotak</th>
                        <th width="15%" class="text-left">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($reimburse as $item)
                    <tr>
                        <td class="text-left">{{ $loop->iteration }}</td>
                        <td class="text-left">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td class="text-left">
                            @if ($item->foto)
                                <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                    data-src="{{ asset('storage/images/reimburse/' . $item->foto) }}">
                                    <img class="reimburse-image" src="{{ asset('storage/images/reimburse/' . $item->foto) }}"
                                        alt="Foto Reimburse">
                                </a>
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                        <td class="text-left">{{ App\Models\Karyawan::find($item->kd_karyawan)->nama }}</td>
                        <td class="keterangan-column text-left">{!! $item->keterangan !!}</td>
                        <td class="text-left">{{ App\Models\Keuangan_Kategori::find($item->kategori)->nama }}</td>
                        <td class="text-left">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                        <td class="text-left">
                            @if ($item->status == '0')
                                <span class="badge badge-info">Menunggu</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->bukti_transfer)
                                <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                    data-src="{{ asset('storage/images/reimburse/' . $item->bukti_transfer) }}">
                                    <img class="reimburse-image"
                                        src="{{ asset('storage/images/reimburse/' . $item->bukti_transfer) }}"
                                        alt="Foto Bukti Transfer">
                                </a>
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                        @if (Auth::user()->role == 'superadmin')
                            <td>{{ App\Models\Keuangan_Kotak::find($item->kotak)->nama ?? '-' }}</td>
                            <td class="text-left">
                                @if ($item->status == '0')
                                    <button class="btn btn-success btn-sm tombol-selesaikan" data-toggle="modal"
                                        data-target="#selesaikanModal" data-id="{{ $item->kd_reimburse }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @else
                                    <button class="btn btn-warning btn-sm tombol-selesaikan" data-toggle="modal"
                                        data-target="#selesaikanModal" data-id="{{ $item->kd_reimburse }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid" alt="Reimburse Image">
            </div>
        </div>
    </div>
</div>
@if (Auth::user()->role == 'superadmin')
    <x-adminlte-modal id="selesaikanModal" title="Bukti Transfer" theme="primary" icon="fas fa-edit" size='lg'>
        <form method="POST" id="form-selesaikan" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="status" value="1">
            <div class="mb-3">
                <img id="preview" src="" alt="Image preview"
                    style="max-width: 20%; display: block; padding: 5px;display:none;">
            </div>
            <div class="form-group">
                <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name
                    onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />
            </div>
            <div class="form-group">
                @php
                    $kotak = App\Models\Keuangan_Kotak::all();
                    $select2 = [
                        'placeholder' => 'Pilih Kotak...',
                        'allowClear' => true,
                        'dropdownParent' => '#selesaikanModal',
                    ];
                @endphp
                <x-adminlte-select2 name="kotak" :config="$select2" label="Kotak" required>
                    <option value="" disabled selected>Pilih Kotak</option>
                    @foreach ($kotak as $item)
                        <option value="{{ $item->kd_kotak }}">{{ $item->nama }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="primary" label="Simpan Perubahan" type="submit" form="form-selesaikan" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
@endif

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#ReimburseTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
            scrollX: true,
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
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        $(document).ready(function () {
            $('#ReimburseTable').on('click', '.tombol-selesaikan', function () {
                console.log('Button was clicked');
                const kd_reimburse = $(this).data('id');

                let form = $('#form-selesaikan');
                let updateUrl = "{{ route('reimburse.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', kd_reimburse);
                console.log(updateUrl);
                form.attr('action', updateUrl);
            });
        });

        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });

        $('#ReimburseTable').on('click', '.tombol-hapus', function (e) {
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
                });
                Toast.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                })
            @endif
    });
</script>
@stop