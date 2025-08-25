@extends('adminlte::page')

@section('title', 'Rekap Bulanan')

@section('content_header')
<h1>Rekap Bulanan</h1>
@stop

@section('css')
    <style>
        .editable {
            border-radius: .25rem;
            border: 1px solid #ced4da;
            width: 100px;
        }

        .editable:focus {
            border-color: #80bdff;
            outline: 0;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('presensi.bulanan.create') }}" method="POST" id="presensiBulananForm">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="kd_karyawan" label="Pilih Karyawan" empty-option="Pilih Karyawan..."
                        required>
                        <x-adminlte-options :options="$karyawans->pluck('nama', 'kd_karyawan')->toArray()"
                            empty-option="Pilih Karyawan..."
                            :selected="old('kd_karyawan')" />
                    </x-adminlte-select>
                </div>
                <div class="col-md-3">
                    <x-adminlte-select name="bulan" label="Pilih Bulan" required>
                        <x-adminlte-options :options="[
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ]" empty-option="Pilih Bulan..."
                        :selected="old('bulan')" />
                    </x-adminlte-select>
                </div>
                <div class="col-md-3">
                    <x-adminlte-input name="tahun" label="Tahun" type="number"
                        value="{{ old('tahun', date('Y')) }}"></x-adminlte-input>
                </div>
                <button type="submit" class="btn btn-primary" form="presensiBulananForm">Tambahkan Presensi</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rekap Bulanan</h3>
    </div>
    <div class="card-body">
        <table id="bulananTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Bulan</th>
                    <th class="text-center">Nama Karyawan</th>
                    <th class="text-center">Jumlah Tanggal</th>
                    <th class="text-center">Jumlah Libur</th>
                    <th class="text-center">Jumlah Hari Kerja</th>
                    <th class="text-center">Jumlah Hari Sakit</th>
                    <th class="text-center">Jumlah Hari Izin</th>
                    <th class="text-center">Jumlah Fingerprint</th>
                    <th class="text-center">Jumlah Alpha</th>
                    <th class="text-center">Jumlah Terlambat</th>
                    <th class="text-center">Jumlah Jam Izin</th>
                    <th class="text-center">Jumlah Hari Lembur</th>
                    <th class="text-center">Jumlah Jam Lembur</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" width="150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapBulanan as $rekap)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $rekap->tahun }}</td>
                        <td>{{ \Carbon\Carbon::parse('2022-' . $rekap->bulan . '-01')->locale('id_ID')->translatedFormat('F') }}
                        </td>
                        <td>{{ $karyawans->where('kd_karyawan', $rekap->kd_karyawan)->first()->nama }}</td>
                        <td>{{ $rekap->jumlah_tanggal }}</td>
                        <td>{{ $rekap->jumlah_libur }}</td>
                        <td>{{ $rekap->jumlah_hari_kerja_normal }}</td>
                        <td>{{ $rekap->jumlah_hari_sakit }}</td>
                        <td>{{ $rekap->jumlah_hari_izin }}</td>
                        <td>{{ $rekap->jumlah_fingerprint }}</td>
                        <td>{{ $rekap->jumlah_alpha }}</td>
                        <td>{{ $rekap->jumlah_terlambat }}</td>
                        <td>{{ $rekap->jumlah_jam_izin }}</td>
                        <td>{{ $rekap->jumlah_hari_lembur }}</td>
                        <td>{{ $rekap->jumlah_jam_lembur }}</td>
                        <td>{{ $rekap->verifikasi }}</td>
                        <td>
                            @if ($rekap->verifikasi == '0')
                                <form action="{{ route('presensi.bulanan.verify', $rekap) }}" method="POST" style="display: inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('presensi.bulanan') }}" method="GET" style="display: inline">
                                <input type="hidden" name="kd_presensi_bulanan" value="{{ $rekap->kd_presensi_bulanan }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@if (isset($dataBulanan))
    <section id="dataBulanan">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Presensi {{ $karyawan->nama }} pada bulan
                    {{ \Carbon\Carbon::createFromDate($dataBulanan->tahun, $dataBulanan->bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('presensi.bulanan.update', $dataBulanan) }}" method="POST">
                    <input type="hidden" name="kd_presensi_bulanan" value="{{ $dataBulanan->kd_presensi_bulanan }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kd_presensi_bulanan" value="{{ $dataBulanan->kd_presensi_bulanan }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Tanggal">Jumlah Tanggal:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_tanggal"
                                value="{{ $dataBulanan->jumlah_tanggal }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_tanggal }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Libur">Jumlah Libur:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_libur"
                                value="{{ $dataBulanan->jumlah_libur }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_libur }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Hari Kerja Normal">Jumlah Hari Kerja Normal:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_hari_kerja_normal"
                                value="{{ $dataBulanan->jumlah_hari_kerja_normal }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_hari_kerja_normal }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Hari Sakit">Jumlah Hari Sakit:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_hari_sakit"
                                value="{{ $dataBulanan->jumlah_hari_sakit }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_hari_sakit }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Hari Izin">Jumlah Hari Izin:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_hari_izin"
                                value="{{ $dataBulanan->jumlah_hari_izin }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_hari_izin }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Fingerprint">Jumlah Fingerprint:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_fingerprint"
                                value="{{ $dataBulanan->jumlah_fingerprint }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_fingerprint }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Alpha">Jumlah Alpha:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_alpha"
                                value="{{ $dataBulanan->jumlah_alpha }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_alpha }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Terlambat">Jumlah Terlambat:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_terlambat"
                                value="{{ $dataBulanan->jumlah_terlambat }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_terlambat }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Jam Izin">Jumlah Jam Izin:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="editable" name="jumlah_jam_izin"
                                value="{{ $dataBulanan->jumlah_jam_izin }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_jam_izin }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Hari Lembur">Jumlah Hari Lembur:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="editable" name="jumlah_hari_lembur"
                                value="{{ $dataBulanan->jumlah_hari_lembur }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_hari_lembur }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="Jumlah Jam Lembur">Jumlah Jam Lembur:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="editable" name="jumlah_jam_lembur"
                                value="{{ $dataBulanan->jumlah_jam_lembur }}">
                        </div>
                        <div class="col-md-4">
                            <span>{{ $dataBulanan->jumlah_jam_lembur }}</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                <form action="{{ route('presensi.bulanan.sync', $dataBulanan) }}" method="POST" class="mt-1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kd_presensi_bulanan" value="{{ $dataBulanan->kd_presensi_bulanan }}">
                    <input type="hidden" name="bulan" value="{{ $dataBulanan->bulan }}">
                    <input type="hidden" name="tahun" value="{{ $dataBulanan->tahun }}">
                    <button type="submit" class="btn btn-info"><i class="fas fa-sync"></i>Auto Sync</button>
                </form>
            </div>
    </section>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Presensi {{ $karyawan->nama }} pada bulan
                {{ \Carbon\Carbon::createFromDate($dataBulanan->tahun, $dataBulanan->bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
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
@endif

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#bulananTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: -1,
            scrollY: '300px',
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
        if ($('#dataBulanan').length) {
            $('html, body').animate({
                scrollTop: $('#dataBulanan').offset().top
            }, 1000);
        }
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
    const formInputs = $('#edit-form input');

    $('#tombol-edit').on('click', function () {
        toggleEditMode(true);
    });

    $('#tombol-batal').on('click', function () {
        toggleEditMode(false);
    });

    function toggleEditMode(enable) {
        if (enable) {
            formInputs.removeAttr('readonly');
            $('#tombol-edit').hide();
            $('#tombol-simpan, #tombol-batal').show();
        } else {
            formInputs.attr('readonly', true);
            $('#tombol-edit').show();
            $('#tombol-simpan, #tombol-batal').hide();
        }
    }

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