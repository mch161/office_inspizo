{{-- resources/views/presensi/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Presensi Logs')

@section('content_header')
    <h1>Presensi Logs</h1>
@stop

@section('content')
            <table id="presensiTable" class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th width='5%'>ID</th>
                        <th width="300px">Date</th>
                        <th width="100px">Time</th>
                        <th width="100px">Verified</th>
                        <th width="100px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->user_id }}</td>
                            <td>{{ $log->timestamp->format('d F Y') }}</td>
                            <td>{{ $log->timestamp->format('H:i:s') }}</td>
                            <td>
                                @if($log->verified)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>{{ $log->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
@stop

{{-- This section is for the JavaScript to initialize the DataTable --}}
@section('js')
    <script>
        $(function () {
            $("#presensiTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "scrollX": true,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                // Set default order: Date (desc), then Time (desc)
                "order": [[ 1, "desc" ], [ 2, "desc" ]]
            }).buttons().container().appendTo('#presensiTable_wrapper .col-md-6:eq(0)');
        });
    </script>
@stop
