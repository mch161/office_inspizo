@extends('adminlte::page')

@section('title', 'Detail Pesanan')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

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

        .edit-mode-1,
        .edit-mode-2,
        .edit-mode-3,
        .edit-mode-4 {
            border: 1px solid #ced4da;
            border-radius: .25rem;
            width: 100px;
        }
    </style>
@endsection

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Detail Pesanan #{{ $pesanan->kd_pesanan }}</h1>
@stop

@section('content')
    <x-adminlte-modal id="barangModal" title="Tambahkan barang" theme="primary">
        <form id="barangForm" action="{{ route('pesanan.barang.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kd_pesanan_detail" value="{{ $pesanan_detail->kd_pesanan_detail }}">
            <x-adminlte-select2 name="kd_barang" label="Barang">
                <option class="text-muted" value="" selected disabled>Cari barang...</option>
                @foreach ($barang as $barang)
                    <option value="{{ $barang->kd_barang }}">{{ $barang->nama_barang }}</option>
                @endforeach
            </x-adminlte-select2>
            <x-adminlte-input name="jumlah" type="number" label="Jumlah" placeholder="Jumlah" />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="barangForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    {{-- /barang modal --}}
    {{-- jasa modal --}}
    <x-adminlte-modal id="jasaModal" title="Tambahkan jasa" theme="primary">
        <form action="{{ route('pesanan.jasa.store') }}" id="jasaForm" method="POST">
            @csrf
            <input type="hidden" name="kd_pesanan_detail" value="{{ $pesanan_detail->kd_pesanan_detail }}">
            <x-adminlte-select2 name="kd_jasa" label="Jasa">
                <option class="text-muted" value="" selected disabled>Cari jasa...</option>
                @foreach ($jasa as $jasa)
                    <option value="{{ $jasa->kd_jasa }}">{{ $jasa->nama_jasa }}</option>
                @endforeach
            </x-adminlte-select2>
            <x-adminlte-input type="number" name="jumlah" label="Jumlah" placeholder="Jumlah" />
            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="jasaForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    {{-- /jasa modal --}}

    <!-- CARD -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Detail Pesanan</h3>
                    <div class="card-tools float-end">
                        <a href="{{ route('pesanan.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Kode Pesanan:</strong> {{ $pesanan->kd_pesanan }}</p>
                            <p><strong>Nama Pelanggan:</strong> {{ $pesanan->pelanggan->nama_pelanggan }}</p>
                            <p><strong>Alamat:</strong> {{ $pesanan->pelanggan->alamat }}</p>
                            <p><strong>Telepon:</strong> {{ $pesanan->pelanggan->telepon }}</p>
                        </div>
                        <a href="{{ route('galeri.index', ['pesanan' => $pesanan->kd_pesanan]) }}"
                            class="btn btn-primary mr-2"><i class="fas fa-images"></i> Galeri</a>
                        @if ($pesanan->progres >= '3')
                            <a class="btn btn-primary mr-2"
                                href="{{ route('progress.index', ['pesanan' => $pesanan->kd_pesanan]) }}"><i
                                    class="fas fa-chart-line"></i> Progress</a>
                        @endif
                        @if ($pesanan->status == '0' && $pesanan->progres >= '3')
                            <form action="{{ route('pesanan.complete', ['pesanan' => $pesanan->kd_pesanan]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Selesaikan</button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <td>{{ $barang->nama_barang }}</td>
                                    <form class="update-form-barang"
                                        action="{{ route('pesanan.barang.update', $barang->kd_pesanan_barang) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="kd_barang" value="{{ $barang->kd_barang }}">
                                        <td class="harga-jual-edit">
                                            <span
                                                class="display-mode-1">{{ number_format($barang->harga_jual ?? $barang->barang->harga, 2, ',', '.') }}</span>
                                            <input type="number" class="edit-mode-1 d-none" name="harga_jual"
                                                value="{{ $barang->harga_jual ?? $barang->barang->harga }}">
                                        </td>
                                        <td class="jumlah-edit">
                                            <span class="display-mode-2">{{ number_format($barang->jumlah) }}</span>
                                            <input type="number" class="edit-mode-2 d-none" name="jumlah"
                                                value="{{ $barang->jumlah }}">
                                        </td>
                                    </form>
                                    <td>{{ number_format($barang->jumlah * ($barang->harga_jual ?? $barang->barang->harga), 2, ',', '.') }}
                                    </td>
                                    <td>
                                        <form action="{{ route('pesanan.barang.destroy', $barang->kd_pesanan_barang) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                                                <i class="fas fa-trash"></i>
                                                Hapus</button>
                                        </form>
                                    </td>
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
                                    <th>Tarif</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pesanan_jasa as $jasa)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $jasa->nama_jasa }}</td>
                                        <form class="update-form-jasa"
                                            action="{{ route('pesanan.jasa.update', $jasa->kd_pesanan_jasa) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="kd_jasa" value="{{ $jasa->kd_jasa }}">
                                            <td class="harga-jasa-edit">
                                                <span
                                                    class="display-mode-3">{{ number_format($jasa->harga_jasa, 2, ',', '.') }}</span>
                                                <input type="number" class="edit-mode-3 d-none" name="harga_jasa"
                                                    value="{{ $jasa->harga_jasa }}">
                                            </td>
                                            <td class="jumlah-jasa-edit">
                                                <span class="display-mode-4">{{ number_format($jasa->jumlah) }}</span>
                                                <input type="number" class="edit-mode-4 d-none" name="jumlah"
                                                    value="{{ $jasa->jumlah }}">
                                            </td>
                                        </form>
                                        <td>{{ number_format($jasa->subtotal, 2, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('pesanan.jasa.destroy', $jasa->kd_pesanan_jasa) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                                                    <i class="fas fa-trash"></i>
                                                    Hapus</button>
                                            </form>
                                        </td>
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
        $(document).ready(function () {
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
            $('#barangTable, #jasaTable').on('click', '.tombol-hapus', function (e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });
        $(document).ready(function () {
            function saveOrRevertChanges(element) {
                const editingRow = $(element).closest('tr');
                if (!editingRow.length) return;

                const newHarga = editingRow.find('input.edit-mode-1').val();
                const newJumlah = editingRow.find('input.edit-mode-2').val();
                const newHargaJasa = editingRow.find('input.edit-mode-3').val();
                const newJumlahJasa = editingRow.find('input.edit-mode-4').val();

                const oldHargaFormatted = editingRow.find('span.display-mode-1').text();
                const oldJumlahFormatted = editingRow.find('span.display-mode-2').text();
                const oldHargaJasaFormatted = editingRow.find('span.display-mode-3').text();
                const oldJumlahJasaFormatted = editingRow.find('span.display-mode-4').text();

                const oldHarga = oldHargaFormatted.split(',')[0].replace(/\./g, '');
                const oldJumlah = oldJumlahFormatted.replace(/\./g, '');
                const oldHargaJasa = oldHargaJasaFormatted.split(',')[0].replace(/\./g, '');
                const oldJumlahJasa = oldJumlahJasaFormatted.replace(/\./g, '');

                const hasChangedBarang = newHarga !== oldHarga || newJumlah !== oldJumlah;
                const hasChangedJasa = newHargaJasa !== oldHargaJasa || newJumlahJasa !== oldJumlahJasa;

                editingRow.find('.is-editing').removeClass('is-editing');
                editingRow.find('.display-mode-1, .display-mode-2, .display-mode-3, .display-mode-4').removeClass('d-none');
                editingRow.find('.edit-mode-1, .edit-mode-2, .edit-mode-3, .edit-mode-4').addClass('d-none');

                if (hasChangedBarang) {
                    editingRow.find('form.update-form-barang').submit();
                }

                if (hasChangedJasa) {
                    editingRow.find('form.update-form-jasa').submit();
                }
            }

            $('#barangTable').on('keyup', '.edit-mode-1, .edit-mode-2', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    saveOrRevertChanges(this);
                }
            });

            $('#jasaTable').on('keyup', '.edit-mode-3, .edit-mode-4', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    saveOrRevertChanges(this);
                }
            });

            $(document).on('click', function (e) {
                const editingCell = $('td.is-editing');
                if (editingCell.length && !$(e.target).closest('td.is-editing').length) {
                    saveOrRevertChanges(editingCell);
                }
            });

            $('#barangTable').on('click', '.harga-jual-edit, .jumlah-edit', function (e) {
                const cell = $(this);
                if (cell.hasClass('is-editing')) return;

                const otherEditingCell = $('td.is-editing');
                if (otherEditingCell.length) {
                    saveOrRevertChanges(otherEditingCell);
                }

                cell.addClass('is-editing');
                cell.find('span').addClass('d-none');
                cell.find('input').removeClass('d-none').focus();
            });

            $('#jasaTable').on('click', '.harga-jasa-edit, .jumlah-jasa-edit', function (e) {
                const cell = $(this);
                if (cell.hasClass('is-editing')) return;

                const otherEditingCell = $('td.is-editing');
                if (otherEditingCell.length) {
                    saveOrRevertChanges(otherEditingCell);
                }

                cell.addClass('is-editing');
                cell.find('span').addClass('d-none');
                cell.find('input').removeClass('d-none').focus();
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
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
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
                    escapeMarkup: false,
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
@endsection