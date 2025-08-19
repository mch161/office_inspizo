@extends('adminlte::page')

@section('title', 'Presensi Bulanan')

@section('content_header')
<h1>Presensi Bulanan</h1>
@stop

@section('css')
    <style>
        .editable {
            border-radius: .25rem;
            border: 1px solid #ced4da;
        }

        .editable:focus {
            border-color: #80bdff;
            outline: 0;
        }

        .editable[readonly] {
            background-color: transparent;
            border: 0;
            box-shadow: none;
            padding-left: 0;
            height: 20px;
            color: currentColor;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('presensi.bulanan') }}" method="GET">
            @if (Auth::guard('karyawan')->user()->role == 'superadmin')
                <x-adminlte-select name="kd_karyawan" label="Pilih Karyawan" empty-option="Pilih Karyawan..." required>
                    <x-adminlte-options :options="$karyawans->pluck('nama', 'kd_karyawan')->toArray()"
                        empty-option="Pilih Karyawan..." />
                </x-adminlte-select>
            @else
                <input type="hidden" name="kd_karyawan" value="{{ Auth::guard('karyawan')->user()->kd_karyawan }}">
            @endif
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
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Data Presensi Bulanan {{ $karyawan->nama }} Pada
                {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id_ID')->translatedFormat('F Y') }}
            </h3>
            <div class="card-tools float-right">
                <button type="button" id="tombol-edit" class="btn btn-sm btn-warning">Edit</button>
                <button type="submit" form="edit-form" id="tombol-simpan" class="btn btn-sm btn-success"
                    style="display: none;">Simpan</button>
                <button type="button" id="tombol-batal" class="btn btn-sm btn-secondary"
                    style="display: none;">Batal</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                Verifikasi: {{ $rekapBulanan->verifikasi == 1 ? 'Sudah' : 'Belum' }}
            </div>
            <form action="{{ route('presensi.bulanan.update') }}" method="POST" id="edit-form" class="form-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="kd_presensi_bulanan" value="{{ $rekapBulanan->kd_presensi_bulanan }}">
                <div class="col-md-12">
                    Jumlah Tanggal: <input type="number" class="editable" name="jumlah_tanggal"
                        value="{{ $rekapBulanan->jumlah_tanggal }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Hari Libur: <input type="number" class="editable" name="jumlah_libur"
                        value="{{ $rekapBulanan->jumlah_libur }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Hari Kerja: <input type="number" class="editable" name="jumlah_hari_kerja_normal"
                        value="{{ $rekapBulanan->jumlah_hari_kerja_normal }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Hari Sakit: <input type="number" class="editable" name="jumlah_hari_sakit"
                        value="{{ $rekapBulanan->jumlah_hari_sakit }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Hari Izin: <input type="number" class="editable" name="jumlah_hari_izin"
                        value="{{ $rekapBulanan->jumlah_hari_izin }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Fingerprint: <input type="number" class="editable" name="jumlah_fingerprint"
                        value="{{ $rekapBulanan->jumlah_fingerprint }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Alpha: <input type="number" class="editable" name="jumlah_alpha"
                        value="{{ $rekapBulanan->jumlah_alpha }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Terlambat: <input type="number" class="editable" name="jumlah_terlambat"
                        value="{{ $rekapBulanan->jumlah_terlambat }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Jam Izin: <input type="time" class="editable" name="jumlah_jam_izin"
                        value="{{ $rekapBulanan->jumlah_jam_izin }}" readonly>
                </div>
                <div class="col-md-12">
                    Jumlah Hari Lembur: <input type="number" class="editable" name="jumlah_hari_lembur"
                        value="{{ $rekapBulanan->jumlah_hari_lembur }}" readonly>
                </div>
                <div class="col-md-12 mb-3">
                    Jumlah Jam Lembur: <input type="time" class="editable" name="jumlah_jam_lembur"
                        value="{{ $rekapBulanan->jumlah_jam_lembur }}" readonly>
                </div>
            </form>
            @if ($rekapBulanan->verifikasi == 0 && Auth::guard('karyawan')->user()->role == 'superadmin')
                <form id="refresh" action="{{ route('presensi.bulanan.sync') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="kd_presensi_bulanan" value="{{ $rekapBulanan->kd_presensi_bulanan }}">
                    <input type="hidden" name="kd_karyawan" value="{{ $rekapBulanan->kd_karyawan }}">
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <div class="col-md-12">
                        <button form="refresh" type="submit" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Auto Sync
                        </button>
                    </div>
                </form>
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
    const formInputs = $('#edit-form input');

    $('#tombol-edit').on('click', function () {
        toggleEditMode(true);
    });

    $('#tombol-batal').on('click', function () {
        toggleEditMode(false);
    });

    // $('#profile-form').on('submit', function (e) {
    //     e.preventDefault();
    //     saveProfileData();
    // });

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