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
            {{-- The calendar will be rendered inside this div --}}
            <div id='calendar'></div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function () {

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendarEl = document.getElementById('calendar');

    // Initialize FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        events: "{{ route('fetch') }}",

        // CREATE event by clicking on a date
        select: function (info) {
            Swal.fire({
                title: 'Create New Event',
                input: 'text',
                inputPlaceholder: 'Enter event title',
                showCancelButton: true,
                confirmButtonText: 'Save',
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
                            Swal.fire('Success!', 'Event created successfully!', 'success');
                        }
                    });
                }
            });
        },

        // UPDATE event by dragging and dropping
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
                    Swal.fire('Success!', 'Event updated successfully!', 'success');
                }
            });
        },

        // DELETE event by clicking on it
        eventClick: function (info) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this event?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('agenda.ajax') }}",
                        data: { id: info.event.id, type: 'delete' },
                        success: function (response) {
                            info.event.remove();
                            Swal.fire('Deleted!', 'Event has been deleted.', 'success');
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
