@extends('adminlte::page')

@section('title', 'Riwayat Stok')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content_header')
<h1>Riwayat Stok</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table id="stokTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Klasifikasi</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stok as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($s->created_at)->format('d-m-Y H:i') }}</td>
                        <td>{{ $s->barang->nama_barang ?? 'N/A' }}</td>
                        <td>
                            @if ($s->klasifikasi == 'Stok Masuk' || $s->klasifikasi == 'Stok Awal')
                                <span class="badge badge-success">{{ $s->klasifikasi }}</span>
                            @else
                                <span class="badge badge-danger">{{ $s->klasifikasi }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($s->stok_masuk > 0)
                                +{{ $s->stok_masuk }}
                            @else
                                -{{ $s->stok_keluar }}
                            @endif
                        </td>
                        <td>{{ $s->keterangan ?? '-' }}</td>
                        <td>{{ $s->dibuat_oleh ?? 'Sistem' }}</td>
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
        $('#stokTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            scrollX: true,
            order: [[0, "desc"]],
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                searchPlaceholder: "Cari data..."
            }
        })
    });
</script>
@endsection