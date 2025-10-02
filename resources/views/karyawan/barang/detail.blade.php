@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
<h1>Barang</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Barang</h3>
                <div class="card-tools">
                    <a href="{{ route('barang.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if ($barang->foto)
                            <a class="image-popup" data-toggle="modal" data-target="#imageModal"
                                data-src="{{ asset('storage/images/barang/' . $barang->foto) }}">
                                <img class="img-fluid barang-image"
                                    src="{{ asset('storage/images/barang/' . $barang->foto) }}" alt="Foto Barang">
                            </a>
                        @else
                            <div class="text-center">
                                <i class="fas fa-camera-retro fa-5x text-muted mb-3"></i>
                                <p class="text-center text-muted m-1">Tidak ada foto.</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <p><strong>Nama Barang:</strong> {{ $barang->nama_barang }}</p>
                        <p><strong>Stok:</strong> {{ $barang->stok }}</p>
                        <p><strong>Kategori:</strong> {{ $barang->kategori ?? '-' }}</p>
                        <p><strong>Harga:</strong> Rp{{ number_format($barang->harga_jual, 2, ',', '.') }}</p>
                        <p><strong>HPP:</strong> Rp{{ number_format($barang->hpp, 2, ',', '.') }}</p>
                        <p><strong>Kode:</strong> {{ $barang->kode ?? '-' }}</p>
                        @if (!is_null($barang->barcode))
                            <p><strong>Barcode:</strong> {{ $barang->barcode }}</p>
                            {!! $barcodeIMG !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Stok</h3>
            </div>
            <div class="card-body">
                <table id="stokTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Stok</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stok as $stok)
                            <tr>
                                <td>{{ $stok->created_at->format('d-m-Y') }}</td>
                                <td>{{ $stok->stok_masuk - $stok->stok_keluar }}</td>
                                <td>{{ $stok->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document"
        style="display: flex; align-items: center; justify-content: center;">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
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
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Cari:",
                    searchPlaceholder: "Cari data..."
                }
            });
        });
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });
    </script>
@endsection