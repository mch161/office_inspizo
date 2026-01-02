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

        .editable:read-only {
            background-color: #e9ecef;
            border: none;
            width: auto;
        }

        .editable:focus {
            border-color: #80bdff;
            outline: 0;
            background-color: #fff;
        }
    </style>
@endsection

@section('content')
{{-- Superadmin: Form to Generate Data --}}
@can('superadmin')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Rekap Baru</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('presensi.bulanan.store') }}" method="POST" id="generateForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-select name="kd_karyawan" label="Karyawan (Opsional)">
                            <option value="">Semua Karyawan</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->kd_karyawan }}">{{ $karyawan->nama }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-select name="bulan" label="Bulan" required>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="tahun" label="Tahun" type="number" value="{{ date('Y') }}" required />
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary w-100"
                            onclick="this.disabled=true;this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Processing...';this.form.submit();">
                            <i class="fas fa-sync"></i> Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- Filter / Select Report --}}
<div class="card">
    <div class="card-body">
        <form action="{{ route('presensi.bulanan') }}" method="GET">
            <div class="row">
                <div class="col-md-10">
                    <x-adminlte-select name="kd_presensi_bulanan" label="Pilih Laporan Bulanan"
                        onchange="this.form.submit()">
                        <option value="" selected disabled>-- Pilih Laporan --</option>
                        @foreach ($rekapBulanan as $item)
                            <option value="{{ $item->kd_presensi_bulanan }}" {{ request('kd_presensi_bulanan') == $item->kd_presensi_bulanan ? 'selected' : '' }}>
                                {{ $item->karyawan->nama ?? '-' }} - {{ date('F', mktime(0, 0, 0, $item->bulan, 1)) }}
                                {{ $item->tahun }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <a href="{{ route('presensi.bulanan') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Detail Data --}}
@if(isset($dataBulanan) && $dataBulanan)
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Detail Rekap: {{ $dataBulanan->karyawan->nama }}
                ({{ $dataBulanan->bulan }}/{{ $dataBulanan->tahun }})</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('presensi.bulanan.create') }}" method="POST">
                @csrf
                <input type="hidden" name="kd_presensi_bulanan" value="{{ $dataBulanan->kd_presensi_bulanan }}">

                {{-- Totals Section --}}
                <div class="row">
                    @php
                        $fields = [
                            'jumlah_tanggal' => 'Hari Kalender',
                            'jumlah_hari_kerja_normal' => 'Hari Kerja',
                            'jumlah_libur' => 'Libur Nasional',
                            'jumlah_hari_minggu' => 'Hari Minggu',
                            'jumlah_fingerprint' => 'Hadir (Finger)',
                            'jumlah_hari_sakit' => 'Sakit',
                            'jumlah_hari_izin' => 'Izin',
                            'jumlah_hari_cuti' => 'Cuti',
                            'jumlah_alpha' => 'Alpha',
                            'jumlah_terlambat' => 'Terlambat',
                            'jumlah_hari_lembur' => 'Hari Lembur',
                        ];
                    @endphp

                    @foreach($fields as $key => $label)
                        <div class="col-md-2 col-6 mb-2">
                            <label class="small text-muted">{{ $label }}</label>
                            <input type="number" name="{{ $key }}" class="form-control editable"
                                value="{{ $dataBulanan->$key }}" readonly>
                        </div>
                    @endforeach

                    <div class="col-md-2 col-6 mb-2">
                        <label class="small text-muted">Jam Lembur</label>
                        <input type="text" name="jumlah_jam_lembur" class="form-control editable"
                            value="{{ $dataBulanan->jumlah_jam_lembur }}" readonly>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label class="small text-muted">Jam Izin</label>
                        <input type="text" name="jumlah_jam_izin" class="form-control editable"
                            value="{{ $dataBulanan->jumlah_jam_izin }}" readonly>
                    </div>
                </div>

                @can('superadmin')
                    <div class="mt-3 text-right">
                        <button type="button" class="btn btn-warning" id="tombol-edit" onclick="toggleEditable(true)">
                            <i class="fas fa-edit"></i> Edit Data
                        </button>
                        <button type="submit" class="btn btn-success" id="tombol-simpan" style="display: none;">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-danger" id="tombol-batal" style="display: none;"
                            onclick="toggleEditable(false)">
                            Batal
                        </button>
                    </div>
                @endcan
            </form>

            <hr>

            {{-- Daily Attendance Table --}}
            <h5 class="mt-4 text-secondary">Log Harian</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $row)
                            <tr class="{{ $row->status == 'M' || $row->status == 'L' ? 'table-secondary' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $row->jam_masuk }}</td>
                                <td>{{ $row->jam_keluar }}</td>
                                <td>
                                    @if($row->status == 'I')
                                        <span class="badge badge-info"><i class="fas fa-file-alt"></i> Izin:
                                            {{ $row->jenis }}</span>

                                    @elseif($row->status == 'H')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Hadir</span>

                                    @elseif($row->status == 'L')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-umbrella-beach"></i> Libur
                                        </span>

                                    @elseif($row->status == 'M')
                                        {{-- Hari Minggu --}}
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-coffee"></i> Minggu
                                        </span>

                                    @elseif($row->status == 'A')
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Alpha</span>
                                    @endif
                                </td>
                                <td>{{ $row->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada data periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center mt-4">
        <h5><i class="icon fas fa-info"></i> Pilih Laporan</h5>
        Silakan pilih laporan bulanan pada dropdown di atas untuk melihat detail.
    </div>
@endif
@stop

@section('js')
<script>
    function toggleEditable(enable) {
        const inputs = $('.editable');
        if (enable) {
            inputs.removeAttr('readonly');
            $('#tombol-edit').hide();
            $('#tombol-simpan, #tombol-batal').show();
        } else {
            inputs.attr('readonly', true);
            $('#tombol-edit').show();
            $('#tombol-simpan, #tombol-batal').hide();
            // Optional: Reload page to reset values if cancelled
            if (event.target.id === 'tombol-batal') window.location.reload();
        }
    }
</script>
@stop