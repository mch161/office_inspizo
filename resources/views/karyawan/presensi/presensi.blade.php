@extends('adminlte::page')

@section('title', 'Presensi Logs')

@section('content_header')
<h1>Presensi Logs</h1>
@stop

@section('content')
@if (isset($tanggal))
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
@endif
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Presensi untuk tanggal
            {{ \Carbon\Carbon::parse($tanggal)->locale('id_ID')->translatedFormat('d F Y') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('presensi.fetch', ['tanggal' => $tanggal]) }}" class="btn btn-primary" id="fetch-btn">
                <i class="fas fa-sync"></i> Sinkronkan
            </a>
            <a href="{{ route('presensi.fetch', 'all') }}" class="btn btn-primary" id="fetch-all-btn">
                <i class="fas fa-sync"></i> Sinkronkan Semua
            </a>
        </div>
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
                        <td>
                            @if ($data->terlambat != "Tidak")
                                <div class="badge badge-warning">{{ $data->terlambat }}</div>
                            @else
                                {{ $data->terlambat }}
                            @endif
                        </td>

                        <td>{{ $data->pulang_cepat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@if (Auth::guard('karyawan')->user()->role == 'superadmin')
    <div class="card">
        <div class="card-body">
            <h3>Total Presensi: {{ count($rekapData) }}</h3>
            <form action="{{ route('presensi.bulanan') }}" method="GET">
                <x-adminlte-select name="kd_karyawan" label="Pilih Karyawan" empty-option="Pilih Karyawan..." required>
                    <x-adminlte-options :options="$karyawans->pluck('nama', 'kd_karyawan')->toArray()"
                        empty-option="Pilih Karyawan..." />
                </x-adminlte-select>
                <x-adminlte-select name="bulan" label="Pilih Bulan" required>
                    <x-adminlte-options :options="[
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'April',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ]" empty-option="Pilih Bulan..." />
                </x-adminlte-select>
                <x-adminlte-input name="tahun" label="Tahun" type="number" value="{{ date('Y') }}"></x-adminlte-input>
                <button type="submit" class="btn btn-primary">Tampilkan Presensi</button>
            </form>
        </div>
    </div>
@endif
@if (Auth::guard('karyawan')->user()->role !== 'superadmin')
<div class="card">
    <div class="card-body">
        <h3>Total Presensi: {{ count($rekapData) }}</h3>
        <form action="{{ route('presensi.bulanan') }}" method="GET">
            <input type="hidden" name="kd_karyawan" value="{{ Auth::guard('karyawan')->user()->kd_karyawan }}">
            <x-adminlte-select name="bulan" label="Pilih Bulan" required>
                <x-adminlte-options :options="[
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April', 
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ]" empty-option="Pilih Bulan..." />
            </x-adminlte-select>
            <x-adminlte-input name="tahun" label="Tahun" type="number" value="{{ date('Y') }}"></x-adminlte-input>
            <button type="submit" class="btn btn-primary">Tampilkan Presensi</button>
        </form>
    </div>
</div>
@endif
@stop

@section('js')
<script>
    $('#rekapTable').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: -1,
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
    $('#fetch-btn, #fetch-all-btn').on('click', function (e) {
        e.preventDefault();
        Swal.fire({
            allowOutsideClick: false,
            showConfirmButton: false,
            showCancelButton: false,
        });
        Swal.showLoading();
        window.location.href = $(this).attr('href');
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
@stop