@extends('adminlte::page')

@section('title', 'View Pekerjaan')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1>View Pekerjaan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Detail Pekerjaan</h3>
                    <div class="card-tools">
                        <a href="{{ route('pekerjaan.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_pekerjaan">Nama Pelanggan:</label>
                                <p>{{ $pekerjaan->pelanggan->nama_pelanggan }}</p>
                            </div>
                            <div class="form-group">
                                <label for="nama_pekerjaan">Tanggal:</label>
                                <p>{{ $pekerjaan->tanggal }}</p>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan:</label>
                                <p>{{ $pekerjaan->keterangan_pekerjaan }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_pekerjaan">Nama Karyawan:</label>
                                <p>{{ $pekerjaan->karyawans->pluck('nama')->implode(', ') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="jenis">Jenis:</label>
                                <p>{{ $pekerjaan->jenis }}</p>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                @php
                                    $statusMap = [
                                        'Akan Dikerjakan' => 'info',
                                        'Dalam Proses' => 'primary',
                                        'Ditunda' => 'secondary',
                                        'Dilanjutkan' => 'primary',
                                        'Selesai' => 'success',
                                    ];
                                    $badgeColor = $statusMap[$pekerjaan->status] ?? 'warning';
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ $pekerjaan->status }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('galeri.index', ['type' => 'pekerjaan', 'id' => $pekerjaan->kd_pekerjaan]) }}"
                        class="btn btn-primary mr-2 mb-2"><i class="fas fa-images"></i> Galeri</a>
                    <a href="{{ route('signature.index', ['type' => 'pekerjaan', 'id' => $pekerjaan->kd_pekerjaan]) }}"
                        class="btn btn-primary mr-2 mb-2">
                        <i class="fas fa-signature"></i> Signature
                    </a>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Keterangan Barang</h3>
                </div>
                <div class="card-body">
                    <table id="barangTable" class="table table-centered table-nowrap mb-0 rounded">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(function () {
        var table = $('#barangTable').DataTable({
            processing: true,
            serverSide: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_barang', name: 'nama_barang' },
                { data: 'harga', name: 'harga' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'subtotal', name: 'subtotal' },
                { data: 'aksi', name: 'aksi' },
            ]
        });
    });
</script>
@stop