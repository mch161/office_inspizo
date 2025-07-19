@extends('adminlte::page')

@section('title', 'Presensi Logs')

@section('content_header')
<h1>Presensi Logs</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Data</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('presensi.index') }}" method="GET" class="form-inline">
            <div class="form-group mb-2">
                <label for="tanggal" class="mr-2">Pilih Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <button type="submit" class="btn btn-primary mb-2 ml-2">Tampilkan</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Presensi untuk
            {{ $tanggal ? 'Tanggal: '.\Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') : 'Semua Tanggal' }}</h3>
    </div>
    <div class="card-body">
        <table id="rekapTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Terlambat</th>
                    <th>Pulang Cepat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapData as $data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $data->nama }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $data->jam_masuk }}</td>
                        <td>{{ $data->jam_keluar ?? '--:--:--' }}</td>
                        <td>{{ $data->terlambat }}</td>
                        <td>{{ $data->pulang_cepat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
    $('#rekapTable').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 5,
        scrollX: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ entri",
            zeroRecords: "Tidak ada data presensi yang ditemukan",
            emptyTable: "Tidak ada data presensi untuk tanggal ini",
            info: "Menampilkan halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data presensi yang tersedia",
            infoFiltered: "(difilter dari _MAX_ total entri)",
            search: "Cari:",
            searchPlaceholder: "Cari data..."
        }
    });
</script>
@stop