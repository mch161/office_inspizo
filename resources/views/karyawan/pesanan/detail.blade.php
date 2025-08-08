@extends('adminlte::page')

@section('title', 'Detail Pesanan')

@section('css')
    <style>
        .ticket-progress ul {
            display: flex;
            list-style-type: none;
            padding-left: 0;
            position: relative;
        }

        .ticket-progress li {
            flex: 1;
            position: relative;
            text-align: center;
            padding-top: 40px;
        }

        .ticket-progress li::before {
            content: '';
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #ccc;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .ticket-progress li::after {
            content: '';
            width: 100%;
            height: 2px;
            background-color: #ccc;
            position: absolute;
            top: 9px;
            left: -50%;
            z-index: 1;
        }

        .ticket-progress li:first-child::after {
            content: none;
        }

        .ticket-progress li.completed::before {
            background-color: #28a745;
        }

        .ticket-progress li.completed::after {
            background-color: #28a745;
        }

        .ticket-progress li.active::before {
            background-color: #007bff;
        }

        .ticket-progress li.active::after {
            background-color: #007bff;
        }

        .step-label {
            font-weight: bold;
        }

        .step-info {
            font-size: 0.9em;
            color: #666;
            padding: 5px 10px;
        }
    </style>
@endsection

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Detail Pesanan</h1>
@stop

@section('content')
    {{-- Barang modal --}}
    <x-adminlte-modal id="barangModal" title="Tambahkan barang" theme="primary">
        <x-adminlte-select name="selBasic" onchange="document.querySelector('input[name=harga]').value = event.target.selectedOptions[0].dataset.harga">
                @foreach ($barang as $barang)
                <x-adminlte-options :options="[$barang->kd_barang => $barang->nama_barang]" />
            @endforeach
        </x-adminlte-select>
        <x-adminlte-input name="harga" label="Harga" value="{{ $barang->harga ?? '' }}" />
        <x-adminlte-input name="jumlah" label="Jumlah" type="number" min="1" required />

    </x-adminlte-modal>
    {{-- /barang modal --}}
    {{-- jasa modal --}}
    <x-adminlte-modal id="jasaModal" title="Tambahkan jasa" theme="primary">
        <x-adminlte-input name="iLabel" label="Nama jasa" placeholder="placeholder"/>
        <x-adminlte-input name="iLabel" label="Nama jasa" placeholder="placeholder"/>
        <x-adminlte-input name="iLabel" label="Nama jasa" placeholder="placeholder"/>
    </x-adminlte-modal>
    {{-- /jasa modal --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Barang</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <x-adminlte-button label="Tambahkan barang" class="mb-2 bg-blue" data-toggle="modal"
                        data-target="#barangModal" />
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
                        <tbody>
                            @foreach ($pesanan_barang as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->barang->nama_barang }}</td>
                                    <td>{{ $barang->barang->harga }}</td>
                                    <td>{{ $barang->jumlah }}</td>
                                    <td>{{ $barang->subtotal }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Jasa</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <x-adminlte-button label="Tambahkan Jasa" class="mb-2 bg-blue" data-toggle="modal"
                        data-target="#jasaModal" />
                    <div class="table-responsive">
                        <table id="jasaTable" class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Jasa</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pesanan_jasa as $jasa)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $jasa->nama_jasa }}</td>
                                        <td>{{ $jasa->harga_jasa }}</td>
                                        <td>{{ $jasa->jumlah }}</td>
                                        <td>{{ number_format($jasa->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Subtotal</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <span class="font-weight-bold">Subtotal Barang:</span> Rp.
                        {{ number_format($pesanan_barang->sum('subtotal'), 2, ',', '.') }}<br>
                        <span class="font-weight-bold">Subtotal Jasa:</span> Rp.
                        {{ number_format($pesanan_jasa->sum('subtotal'), 2, ',', '.') }}<br>
                        <span class="font-weight-bold">Total:</span> Rp.
                        {{ number_format($pesanan_jasa->sum('subtotal') + $pesanan_barang->sum('subtotal'), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#barangTable').DataTable({
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
            $('#jasaTable').DataTable({
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
@endsection
