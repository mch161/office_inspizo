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
                        <th width="300px">Tanggal</th>
                        <th width="100px">waktu</th>
                        <th width="100px">Verifikasi</th>
                        <th width="100px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->user_id }}</td>
                            <td>{{ $log->timestamp->format('Y m d') }}</td>
                            <td>{{ $log->timestamp->format('H:i:s') }}</td>
                            <td>
                                @if($log->verified)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>@if ( $log ->status == "0")
                                Masuk
                                @else
                                Pulang
                            @endif</td>
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
                responsive: true,
                lengthChange: false,
                scrollX: true,
                autoWidth: false,
                order: [[1, "desc"], [2, "desc"]],
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
        });
    </script>
@stop
