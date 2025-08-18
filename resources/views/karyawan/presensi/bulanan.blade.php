@extends('adminlte::page')

@section('title', 'Presensi Bulanan')

@section('content_header')
<h1>Presensi Bulanan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Data Presensi Bulanan {{ $karyawan->nama }} Pada
                {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
            </h3>
            <div class="card-tools">
                <div class="float-right">
                    <a href="{{ route('presensi.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                Verifikasi: {{ $rekapBulanan->verifikasi == 1 ? 'Sudah' : 'Belum' }}
            </div>
            <div class="col-md-12">
                Jumlah Tanggal: {{ $rekapBulanan->jumlah_tanggal }}
            </div>
            <div class="col-md-12">
                Jumlah Hari Libur: {{ $rekapBulanan->jumlah_libur }}
            </div>
            <div class="col-md-12">
                Jumlah Hari Kerja: {{ $rekapBulanan->jumlah_hari_kerja_normal }}
            </div>
            <div class="col-md-12">
                Jumlah Hari Sakit: {{ $rekapBulanan->jumlah_hari_sakit }}
            </div>
            <div class="col-md-12">
                Jumlah Hari Izin: {{ $rekapBulanan->jumlah_hari_izin }}
            </div>
            <div class="col-md-12">
                Jumlah Fingerprint: {{ $rekapBulanan->jumlah_fingerprint }}
            </div>
            <div class="col-md-12">
                Jumlah Alpha: {{ $rekapBulanan->jumlah_alpha }}
            </div>
            <div class="col-md-12">
                Jumlah Terlambat: {{ $rekapBulanan->jumlah_terlambat }}
            </div>
            <div class="col-md-12">
                Jumlah Jam Izin: {{ $rekapBulanan->jumlah_jam_izin }}
            </div>
            <div class="col-md-12">
                Jumlah Hari Lembur: {{ $rekapBulanan->jumlah_hari_lembur }}
            </div>
            <div class="col-md-12 mb-3">
                Jumlah Jam Lembur: {{ $rekapBulanan->jumlah_jam_lembur }}
            </div>
            @if ($rekapBulanan->verifikasi == 0)
                <form id="refresh" action="{{ route('presensi.bulanan.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kd_presensi_bulanan" value="{{ $rekapBulanan->kd_presensi_bulanan }}">
                    <input type="hidden" name="kd_karyawan" value="{{ $rekapBulanan->kd_karyawan }}">
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <div class="col-md-12">
                        <button form="refresh" type="submit" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </form>
                @if (Auth::user()->role == 'superadmin')
                    <form id="verify" action="{{ route('presensi.bulanan.verify') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="kd_presensi_bulanan" value="{{ $rekapBulanan->kd_presensi_bulanan }}">
                        <div class="col-md-12">
                            <button form="verify" type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Verifikasi
                            </button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Presensi {{ $karyawan->nama }} pada bulan
            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
        </h3>
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
@stop

@section('js')
<script>
    $(document).ready(function () {
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
    });
</script>
@stop