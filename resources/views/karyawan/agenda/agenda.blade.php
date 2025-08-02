@extends('adminlte::page')

@section('title', 'Agenda')

{{-- These lines load the necessary CSS and JS for the plugins --}}
@section('plugins.Fullcalendar', true)
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1><i class="fas fa-calendar-alt"></i> Agenda</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-body">
            <div id='calendar'></div>
        </div>
        <div class="card-footer">
            <span><i class="fas fa-info-circle"></i> Klik pada tanggal untuk membuat agenda</span>
            <div>Total Hari Minggu: <b><span id="total_hari_minggu"></span></b></div>
            <div>Total Hari Kerja: <b><span id="total_hari_kerja"></span></b></di>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        buttonText: {
            today:    'Hari Ini',
            month:    'Bulan',
            week:     'Minggu',
            day:      'Hari'
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        events: "{{ route('fetch') }}",

        select: function (info) {
            Swal.fire({
                title: 'Buat Agenda',
                input: 'text',
                inputPlaceholder: 'Judul Agenda',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    var title = result.value;
                    var start = info.startStr;
                    var end = info.endStr;
                    
                    $.ajax({
                        url: "{{ route('agenda.ajax') }}",
                        data: { title: title, start: start, end: end, type: 'add' },
                        type: "POST",
                        success: function (data) {
                            calendar.refetchEvents();
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
                            })

                            Toast.fire({
                                icon: 'success',
                                title: 'Agenda berhasil dibuat!'
                            })
                        }
                    });
                }
            });
        },

        eventDrop: function (info) {
            var start = info.event.start.toISOString().slice(0, 19).replace('T', ' ');
            var end = info.event.end ? info.event.end.toISOString().slice(0, 19).replace('T', ' ') : start;
            
            $.ajax({
                url: "{{ route('agenda.ajax') }}",
                data: {
                    title: info.event.title,
                    start: start,
                    end: end,
                    id: info.event.id,
                    type: 'update'
                },
                type: "POST",
                success: function (response) {
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
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Agenda berhasil diperbarui!'
                    })
                }
            });
        },

        eventClick: function (info) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang di ganti tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('agenda.ajax') }}",
                        data: { id: info.event.id, type: 'delete' },
                        success: function (response) {
                            info.event.remove();
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
                                title: 'Agenda berhasil dihapus.'
                            });
                        }
                    });
                }
            });
        }
    });

    calendar.render();
});
</script>
@stop
