@extends('adminlte::page')

@section('title', 'Jurnal')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Jurnal</h1>
@stop

@section('css')
<style>
    .date-scroll-container {
        overflow-x: auto;
        white-space: nowrap;
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .date-scroll-container::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, and Opera */
    }

    .date-scroll-inner {
        display: inline-block;
    }

    .date-btn {
        display: inline-block;
        text-align: center;
        margin: 0 2px;
        border-radius: 8px;
        padding: 8px 12px;
        min-width: 60px;
        border: 1px solid #dee2e6;
    }

    .date-btn .day-name {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .date-btn .day-number {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .date-btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

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
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Jurnal - {{ $tanggal->isoFormat('D MMMM YYYY') }}</h3>
        <div class="card-tools">
            <div class="date" id="monthPicker" data-target-input="nearest">
                <input type="hidden" class="datetimepicker-input" data-target="#monthPicker" />
                <button class="btn btn-outline-primary btn-sm" data-target="#monthPicker" data-toggle="datetimepicker">
                    <i class="fa fa-calendar-alt"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="date-scroll-container">
                    <div class="date-scroll-inner">
                        @foreach($hariBulanIni as $hari)
                            @if($hari->isFuture())
                                <button class="btn btn-outline-{{ $hari->dayOfWeek == 0 ? 'danger' : 'primary' }} btn-sm date-btn" disabled>
                                    <div class="day-name">{{ $hari->isoFormat('ddd') }}</div>
                                    <div class="day-number">{{ $hari->day }}</div>
                                </button>
                            @else
                                <a href="{{ route('jurnal_kita', ['date' => $hari->toDateString()]) }}"
                                    class="btn btn-outline-{{ $hari->dayOfWeek == 0 ? 'danger' : 'primary' }} btn-sm date-btn {{ $tanggal->isSameDay($hari) ? 'active' : '' }}">
                                    <div class="day-name">{{ $hari->isoFormat('ddd') }}</div>
                                    <div class="day-number">{{ $hari->day }}</div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <ul class="timeline">
            @forelse ($jurnals as $jurnal)
                <li class="timeline-item info">
                    <div class="timeline-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="timeline-content">
                        <p class="mb-1">{!! $jurnal->isi_jurnal !!}</p>
                        <span class="timeline-time">
                            {{ $jurnal->tanggal }}({{ $jurnal->jam }}) by {{ $jurnal->dibuat_oleh }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="timeline-item warning">
                    <div class="timeline-icon">
                        <i class="fas fa-hourglass-start"></i>
                    </div>
                    <div class="timeline-content">
                        <p class="mb-1">Belum ada jurnal</p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@stop

@section('js')
    <script>
        $('#monthPicker').datetimepicker({
            format: 'MMMM YYYY',
            viewMode: 'months',
            locale: 'id',
            defaultDate: '{{ $tanggal->toDateString() }}'
        });

        $('#monthPicker').on('change.datetimepicker', function (e) {
            if (e.date) {
                const newDate = e.date.format('YYYY-MM-DD');
                window.location.href = `{{ route('jurnal_kita') }}?date=${newDate}`;
            }
        });
        $(document).ready(function () {
            const $container = $('.date-scroll-container');
            const $activeButton = $('.date-btn.active');

            if ($activeButton.length) {
                const containerWidth = $container.width();
                const buttonPosition = $activeButton.position().left + $container.scrollLeft();
                const buttonWidth = $activeButton.outerWidth();

                const scrollPosition = buttonPosition - (containerWidth / 2) + (buttonWidth / 2);

                $container.scrollLeft(scrollPosition);
            }
        });
        $(document).ready(function () {
            var summernoteOptions = {
                height: 250,
                placeholder: 'Masukkan keterangan jurnal di sini...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            };

            $('#summernote_add').summernote(summernoteOptions);
            $('#summernote_edit').summernote(summernoteOptions);
        });
        $(document).ready(function () {
            $('.tombol-edit').on('click', function () {
                const id = $(this).data('id');
                const tanggal = $(this).data('tanggal');
                const jam = $(this).data('jam');
                const isi_jurnal = $(this).data('isi_jurnal');

                if (!"{{ $errors->any() && session('invalid_jurnal') }}") {

                    $('#tanggal_edit').val(tanggal);
                    $('#jam_edit').val(jam);
                    $('#summernote_edit').summernote('code', isi_jurnal);
                }

                let form = $('#form-edit-jurnal');
                let updateUrl = "{{ url('jurnal') }}/" + id;
                form.attr('action', updateUrl);
            });

            $('.tombol-hapus').on('click', function () {
                e.preventDefault();
                let form = $(this).closest('form');
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
                        form.submit();
                    }
                });
            });

            @if ($errors->any() && session('invalid_jurnal'))
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
                    text: '{{ session('invalid_jurnal') }}',
                })
                const errorJurnalId = "{{ session('error_jurnal_id') }}";

                if (errorJurnalId) {
                    let form = $('#form-edit-jurnal');
                    let updateUrl = "{{ url('jurnal') }}/" + errorJurnalId;
                    form.attr('action', updateUrl);
                }

                $('#modalEdit').modal('show');
            @endif
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
                    });
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
                    });
                @endif
                });
    </script>
@endsection