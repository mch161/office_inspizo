@extends('adminlte::page')

@section('title', 'Pelanggan')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1><i class="fas fa-users"></i> Pelanggan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Pelanggan</h3>
        <div class="card-tools float-end">
            <a href="{{ route('pelanggan.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nama Pelanggan:</strong> {{ $pelanggan->nama_pelanggan }}</p>
                <p><strong>Nama Perusahaan:</strong> {{ $pelanggan->nama_perusahaan }}</p>
                <p><strong>Alamat:</strong> {{ $pelanggan->alamat_pelanggan }}</p>
                <p><strong>No Telepon:</strong> {{ $pelanggan->telp_pelanggan }}</p>
                <p><strong>Username:</strong> {{ $pelanggan->username }}</p>
                <p><strong>Email:</strong> {{ $pelanggan->email }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pesanan</h3>
    </div>
    <div class="card-body">
        Total Pesanan: {{ $pesanan->count() }}
        <table id="pesananTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Deskripsi Pesanan</th>
                    <th>Tanggal</th>
                    <th width="100px">Status</th>
                    <th width="100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesanan as $pesanan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                        <td>{{ $pesanan->tanggal}}</td>
                        <td class="text-center">
                            @if ($pesanan->status == 1)
                                <span class="badge bg-success">Selesai</span>
                            @elseif ($pesanan->status == 0 && $pesanan->progres == 1)
                                <span class="badge bg-info">Pesanan Dibuat</span>
                            @elseif ($pesanan->status == 0 && $pesanan->progres == 2)
                                <span class="badge bg-warning">Pesanan Diterima</span>
                            @elseif ($pesanan->status == 0 && $pesanan->progres == 3)
                                <span class="badge bg-secondary">Pesanan Diproses</span>
                            @elseif ($pesanan->status == 2)
                                <span class="badge bg-danger">Pesanan Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pesanan.detail', $pesanan->kd_pesanan)}}"
                                class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td><span>Tidak ada data</span></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Kunjungan</h3 </div>
        <div class="card-body">
            Total Kunjungan: {{ $kunjungan->count() }}
            <table id="kunjunganTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Keterangan</th>
                        <th width="100px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kunjungan as $kunjungan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kunjungan->tanggal }}</td>
                            <td>{!! $kunjungan->keterangan !!}</td>
                            <td class="text-center">
                                @if ($kunjungan->status == 1)
                                    <span class="badge bg-success">Selesai</span>
                                @elseif ($kunjungan->status == 0)
                                    <span class="badge bg-warning">Belum Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Project</h3>
    </div>
    <div class="card-body">
        Total Project: {{ $project->count() }}
        <table id="projectTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Project</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th width="100px">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($project as $project)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $project->nama_project }}</td>
                        <td>{{ $project->tanggal_mulai }}</td>
                        <td>{{ $project->tanggal_selesai ?? '-' }}</td>
                        <td class="text-center">
                            <span
                                class="badge {{ $project->status == 'Belum Selesai' ? 'badge-warning' : 'badge-success' }} float-right">{{ $project->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('project.detail', $project->kd_project)}}"
                                class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>
                        </td>
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
        $('#pesananTable, #kunjunganTable, #projectTable').DataTable({
            scrollX: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '200px',
            searching: false,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)"
            }
        });
    });
</script>
@stop