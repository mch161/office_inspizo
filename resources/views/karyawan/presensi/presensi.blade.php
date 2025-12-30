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
        <h3 class="card-title">Laporan Presensi untuk
            @if (isset($bulan) && isset($tahun))
                {{ $karyawan->nama }} pada bulan
                {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
            @else
                {{ \Carbon\Carbon::parse($tanggal)->locale('id_ID')->translatedFormat('d F Y') }}
            @endif
        </h3>
        <div class="card-tools">
            @if (isset($bulan) && isset($kd_karyawan))
                <a href="{{ route('presensi.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            @else
                <a href="{{ route('presensi.fetch', ['tanggal' => $tanggal]) }}" class="btn btn-primary" id="fetch-btn">
                    <i class="fas fa-sync"></i> Sinkronkan
                </a>
                <a href="{{ route('presensi.fetch', 'all') }}" class="btn btn-primary" id="fetch-all-btn">
                    <i class="fas fa-sync"></i> Sinkronkan Semua
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        {{-- Daily Attendance Table --}}
        <h5 class="mt-4 text-secondary">Log Harian</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        @if (!isset($bulan) && !isset($kd_karyawan))
                            <th>Nama Karyawan</th>
                        @endif
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        @if (!isset($bulan) && !isset($kd_karyawan))
                            <th>Terlambat</th>
                            <th>Pulang Cepat</th>
                        @else
                            <th>Status</th>
                            <th>Keterangan</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapData as $row)
                        <tr>
                            @if (!isset($bulan) && !isset($kd_karyawan))
                                <td>{{ $row->nama }}</td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $row->jam_masuk ?? '--:--:--'}}</td>
                            <td>{{ $row->jam_keluar ?? '--:--:--'}}</td>
                            @if (!isset($bulan) && !isset($kd_karyawan))
                                <td>
                                    @if ($row->terlambat != "Tidak")
                                        <div class="badge badge-warning">{{ $row->terlambat }}</div>
                                    @else
                                        {{ $row->terlambat }}
                                    @endif
                                </td>
                                <td>{{ $row->pulang_cepat }}</td>
                            @else
                                <td>
                                    @if($row->status == 'I')
                                        <span class="badge badge-info">Izin: {{ $row->jenis }}</span>
                                    @elseif($row->status == 'H')
                                        <span class="badge badge-success">Hadir</span>
                                    @elseif($row->status == 'L')
                                        <span class="badge badge-info">Libur</span>
                                    @elseif($row->status == 'M')
                                        <span class="badge badge-secondary">Minggu</span>
                                    @elseif($row->status == 'A')
                                        <span class="badge badge-danger">Alpha</span>
                                    @endif
                                </td>
                                <td>{{ $row->keterangan }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Belum ada data periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3>Total Presensi: {{ count($rekapData) }}</h3>
        <form action="{{ route('presensi.view') }}" method="GET">
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