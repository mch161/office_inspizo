@extends('adminlte::page')

@section('title', 'Detail Progress Pesanan')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Progress Pesanan #{{ $pesanan->kd_pesanan ?? 'N/A' }}</h1>
@stop

@section('css')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
        list-style: none;
    }

    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 40px;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: 40px;
        top: 0;
        transform: translateX(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #adb5bd;
        /* Default color */
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }

    .timeline-item.success .timeline-icon {
        background-color: #28a745;
    }

    .timeline-item.info .timeline-icon {
        background-color: #17a2b8;
    }

    .timeline-item.warning .timeline-icon {
        background-color: #ffc107;
    }

    .timeline-content {
        margin-left: 75px;
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        position: relative;
    }

    .timeline-content:before {
        content: ' ';
        height: 0;
        position: absolute;
        top: 15px;
        right: 100%;
        width: 0;
        border: medium solid #f8f9fa;
        border-width: 10px 10px 10px 0;
        border-color: transparent #f8f9fa transparent transparent;
    }

    .timeline-content img {
        max-width: 150px;
        height: auto;
        border-radius: 4px;
        margin-top: 10px;
    }

    .timeline-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Progress</h3>
        <div class="card-tools float-end">
            <a href="{{ route('tiket.show', $pesanan) }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="Progress">Progress</label>
                <select name="progress" id="progress" class="form-control">
                    <option value="" selected disabled>Pilih Progress...</option>
                    <option value="1a">Akan melakukan kunjungan</option>
                    <option value="1b">Tiba di lokasi</option>
                    <option value="2">Mengambil barang untuk</option>
                    <option value="3">Melakukan Perbaikan</option>
                    <option value="4">Mengirim barang</option>
                </select>
            </div>

            <!-- Input datetime -->
            <div class="form-group col-md-6 d-none" id="date-field">
                @php $config = ['format' => 'DD-MM-YYYY']; @endphp
                <x-adminlte-input-date name="tanggal" id="tanggal" :config="$config" placeholder="Pilih tanggal..."
                    label="Tanggal" igroup-size="md" required>
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>
            <div class="form-group col-md-6 d-none" id="time-field">
                @php $config = ['format' => 'HH:mm']; @endphp
                <x-adminlte-input-date name="jam" id="jam" :config="$config" placeholder="Pilih jam..." label="Jam"
                    igroup-size="md" required>
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <!-- option for 2 -->
            <div class="form-group col-md-12 d-none" id="option-field">
                <label for="untuk">Untuk</label>
                <select name="untuk" id="untuk" class="form-control">
                    <option value="" selected disabled>Pilih Untuk...</option>
                    <option value="diperiksa">Diperiksa</option>
                    <option value="dilakukan perbaikan">Dilakukan Perbaikan</option>
                </select>
            </div>

            <!-- tempat pengerjaan -->
            <div class="form-group col-md-12 d-none" id="tempat-field">
                <label for="tempat">Tempat</label>
                <select name="tempat" id="tempat" class="form-control">
                    <option value="" selected disabled>Pilih Tempat...</option>
                    <option value="kantor">Kantor</option>
                    <option value="tempat pelanggan">Tempat Pelanggan</option>
                    <option value="(isi)">Lainnya</option>
                </select>
            </div>
        </div>
        <button data-toggle="modal" data-target="#modalTambah" class="btn btn-primary tombol-tambah">Tambah
            Progress</button>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Progress</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <ul class="timeline">
            @forelse ($progress as $p)
            <li
                class="timeline-item @if(Str::contains(strtolower($p->keterangan), 'selesai')) success @else info @endif">
                <div class="timeline-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="timeline-content">
                    <div class="float-end">
                        <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                            data-id="{{ $p->kd_pesanan_progress }}" data-keterangan="{{ $p->keterangan }}">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm tombol-hapus" data-id="{{ $p->kd_pesanan_progress }}">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                    </div>
                    <p class="mb-1">{!! $p->keterangan !!}</p>
                    <span class="timeline-time">
                        by {{ $p->dibuat_oleh }},
                        {{ $p->created_at->format('d M Y, H:i') }}
                        @if ($p->created_at->format('d M Y') == $p->updated_at->format('d M Y') && $p->created_at !=
                        $p->updated_at)
                        (Diedit pada {{ $p->updated_at->format('H:i') }})
                        @elseif ($p->created_at != $p->updated_at)
                        (Diedit pada {{ $p->updated_at->format('d M Y, H:i') }})
                        @endif</span>
                </div>
            </li>
            @empty
            <li class="timeline-item warning">
                <div class="timeline-icon">
                    <i class="fas fa-hourglass-start"></i>
                </div>
                <div class="timeline-content">
                    <p>Belum ada progress yang ditambahkan. Menunggu update dari petugas.</p>
                </div>
            </li>
            @endforelse
        </ul>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<x-adminlte-modal id="modalTambah" theme="info" title="Keterangan" size='lg'>
    <form method="POST" id="form-progress">
        @csrf
        <div class="form-group keterangan-field">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" required></textarea>
        </div>
        <x-slot name="footerSlot">
            <button type="submit" class="btn btn-success" form="form-progress">Simpan</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="modalEdit" theme="info" title="Edit Progress" size='lg'>
    <form method="POST" id="form-edit-progress">
        @csrf
        @method('PUT')
        <input type="hidden" name="kd_pesanan_progress" id="kd_pesanan_progress-edit">
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan-edit" class="form-control" required></textarea>
        </div>
        <x-slot name="footerSlot">
            <button type="submit" class="btn btn-success" form="form-edit-progress">Simpan</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
        </x-slot>
    </form>
</x-adminlte-modal>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        const summernoteOptions = {
            height: 150,
            placeholder: 'Keterangan...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
            ],
        };

        $('#keterangan').summernote(summernoteOptions);
        $('#keterangan-edit').summernote(summernoteOptions);

        $('#progress').change(function () {
            if ($(this).val() == '1a') {
                $('#date-field').removeClass('d-none');
                $('#time-field').removeClass('d-none');
            } else {
                $('#date-field').addClass('d-none');
                $('#time-field').addClass('d-none');
            }
            if ($(this).val() == '2') {
                $('#option-field').removeClass('d-none');
            } else {
                $('#option-field').addClass('d-none');
            }
            if ($(this).val() == '3') {
                $('#tempat-field').removeClass('d-none');
            } else {
                $('#tempat-field').addClass('d-none');
            }
        })

        $('.tombol-tambah').on('click', function () {
            const tanggal = $('#tanggal').val();
            const Jam = $('#jam').val();
            const untuk = $('#untuk').val();
            const tempat = $('#tempat').val();
            var progress = $('#progress').val();
            if (progress == '1a') {
                $('#keterangan').summernote('code', 'Petugas akan melakukan kunjungan ke pelanggan pada tanggal ' + tanggal + ' jam ' + Jam);
            }
            if (progress == '1b') {
                $('#keterangan').summernote('code', 'Petugas tiba dan sedang berkunjungan ke pelanggan');
            }
            if (progress == '2') {
                $('#keterangan').summernote('code', 'Petugas mengambil barang di pelanggan untuk ' + untuk);
            }
            if (progress == '3') {
                $('#keterangan').summernote('code', 'Petugas melakukan perbaikan di ' + tempat);
            }
            if (progress == '4') {
                $('#keterangan').summernote('code', 'Petugas mengirim barang ke pelanggan');
            }
            if (progress == '5') {
                $('#keterangan').summernote('code', '');
            }

            let form = $('#form-progress');
            let storeUrl = "{{ route('progress.store', ['pesanan' => $pesanan]) }}";
            form.attr('action', storeUrl);
        });

        $('.tombol-edit').on('click', function () {
            const progressId = $(this).data('id');
            const keterangan = $(this).data('keterangan');
            $('#kd_pesanan_progress-edit').val(progressId);
            $('#keterangan-edit').summernote('code', keterangan);

            let form = $('#form-edit-progress');
            let updateUrl = "{{ route('progress.update', ['pesanan' => $pesanan, 'progress' => ':id']) }}".replace(':id', progressId);
            form.attr('action', updateUrl);
        });

        $('.tombol-hapus').on('click', function () {
            const progressId = $(this).data('id');
            const deleteUrl = `{{ route('progress.destroy', ['pesanan' => $pesanan, 'progress' => ':id']) }}`.replace(':id', progressId);

            $('#delete-form').attr('action', deleteUrl);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form').submit();
                }
            })
        });


        @if (session() -> has('success'))
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
        @if (session() -> has('error'))
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