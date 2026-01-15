@extends('adminlte::page')

@section('title', 'Detail Tiket')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Detail Tiket #{{ $tiket->kd_tiket }}</h1>
@stop

@section('content')
    <x-adminlte-modal id="barangModal" title="Tambahkan barang" theme="primary">
        <form id="barangForm"
            action="{{ route('pesanan.barang.store', ['jenis' => $pesanan->jenis, 'id' => $billing->kd_quotation ?? $pesanan->kd_invoice]) }}"
            method="POST">
            @csrf
            @php
                $barang_config = [
                    $barang = App\Models\Barang::orderBy('nama_barang', 'asc')->get(),
                    'placeholder' => 'Cari barang...',
                    'allowClear' => true,
                    'tags' => true,
                ]
            @endphp
            <x-adminlte-select2 name="kd_barang" label="Barang" :config="$barang_config">
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
        <form
            action="{{ route('pesanan.jasa.store', ['jenis' => $pesanan->jenis, 'id' => $billing->kd_quotation ?? $pesanan->kd_invoice]) }}"
            id="jasaForm" method="POST">
            @csrf
            @php
                $jasa_config = [
                    $jasa = App\Models\Jasa::orderBy('nama_jasa', 'asc')->get(),
                    'placeholder' => 'Cari jasa...',
                    'allowClear' => true,
                    'tags' => true,
                ]
            @endphp
            <x-adminlte-select2 name="kd_jasa" label="Jasa" :config="$jasa_config">
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
    <x-adminlte-modal id="agendaModal" title="Agendakan Pesanan" theme="primary">
        <form id="agendaForm" method="POST" action="{{ route('tiket.agenda') }}">
            @csrf
            <input type="hidden" name="kd_pesanan" id="kd_pesanan" value="{{ $pesanan->kd_pesanan }}">
            <label for="title">Nama</label>
            <input class="form-control" type="text" name="title" id="title" placeholder="Nama Agenda" required>

            @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" id="tanggal-agenda" value="{{ $pesanan->tanggal }}" :config="$configDate"
                placeholder="Pilih tanggal..." label="Tanggal Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            @php $configTime = ['format' => 'HH:mm']; @endphp
            <x-adminlte-input-date name="jam" id="jam-agenda" :config="$configTime" placeholder="Pilih jam..."
                label="Jam Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            <x-slot name="footerSlot">
                <button type="submit" class="btn btn-primary" form="agendaForm">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            </x-slot>
        </form>
    </x-adminlte-modal>

    <!-- CARD -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Detail Pesanan</h3>
                    <div class="card-tools float-end">
                        <a href="{{ $backUrl }}" class="btn btn-primary">
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
                            <p><strong>Deskripsi Pesanan: </strong> {{ $tiket->deskripsi }}</p>
                        </div>
                        <a href="{{ route('galeri.index', ['type' => 'pesanan', 'id' => $pesanan->kd_pesanan]) }}"
                            class="btn btn-primary mr-2 mb-2"><i class="fas fa-images"></i> Galeri</a>
                        <!-- <a href="{{ route('signature.index', ['type' => 'pesanan', 'id' => $pesanan->kd_pesanan]) }}"
                            class="btn btn-primary mr-2 mb-2">
                            <i class="fas fa-signature"></i> Signature</a> -->
                        <a class="btn btn-primary mr-2 mb-2"
                            href="{{ route('progress.index', ['pesanan' => $pesanan->kd_pesanan]) }}"><i
                                class="fas fa-chart-line"></i> Progress</a>
                        @if ($pesanan->status == 0 && $pesanan->progres == 2)
                            <a class="btn btn-warning mr-2 mb-2" data-toggle="modal" data-target="#agendaModal">
                                <i class="fas fa-calendar-day"></i> Agendakan</a>
                        @endif
                        <form action="{{ route('tiket.complete', ['pesanan' => $pesanan->kd_pesanan]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Selesaikan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Pesanan : {{ $pesanan->jenis }}</h3>
                    <div class="card-tools">
                        @if ($pesanan->jenis == 'Quotation')
                            <a class="btn btn-primary mr-2 mb-2" href="{{ route('tiket.invoice', $pesanan->kd_pesanan) }}"><i
                                    class="fas fa-notebook"></i> Ganti ke Invoice</a>
                        @endif
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
                            @foreach ($billing_barang as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->barang->nama_barang }}</td>
                                    <form class="update-form-barang"
                                        action="{{ route('pesanan.barang.update', ['jenis' => $pesanan->jenis, 'id' => $barang->kd_quotation_item ?? $barang->kd_invoice_item]) }}"
                                        method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="kd_barang" value="{{ $barang->kd_barang }}">
                                        <td class="harga-jual-edit">
                                            <span
                                                class="display-mode-1">{{ number_format($barang->harga ?? $barang->barang->harga, 2, ',', '.') }}</span>
                                            <input type="number" class="edit-mode-1 d-none" name="harga"
                                                value="{{ $barang->harga ?? $barang->barang->harga }}">
                                        </td>
                                        <td class="jumlah-edit">
                                            <span class="display-mode-2">{{ number_format($barang->jumlah) }}</span>
                                            <input type="number" class="edit-mode-2 d-none" name="jumlah"
                                                value="{{ $barang->jumlah }}">
                                        </td>
                                    </form>
                                    <td>{{ number_format($barang->subtotal , 2, ',', '.') }}
                                    </td>
                                    <td>
                                        <form
                                            action="{{ route('pesanan.barang.destroy', ['jenis' => $pesanan->jenis, 'id' => $barang->kd_quotation_item ?? $barang->kd_invoice_item]) }}"
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
                    <hr>
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
                                @foreach ($billing_jasa as $jasa)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $jasa->jasa->nama_jasa }}</td>
                                        <form class="update-form-jasa"
                                            action="{{ route('pesanan.jasa.update', ['jenis' => $pesanan->jenis, 'id' => $jasa->kd_quotation_item ?? $jasa->kd_invoice_item]) }}"
                                            method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="kd_jasa" value="{{ $jasa->kd_jasa }}">
                                            <td class="harga-jasa-edit">
                                                <span
                                                    class="display-mode-3">{{ number_format($jasa->harga, 2, ',', '.') }}</span>
                                                <input type="number" class="edit-mode-3 d-none" name="harga_jasa"
                                                    value="{{ $jasa->harga }}">
                                            </td>
                                            <td class="jumlah-jasa-edit">
                                                <span class="display-mode-4">{{ number_format($jasa->jumlah) }}</span>
                                                <input type="number" class="edit-mode-4 d-none" name="jumlah"
                                                    value="{{ $jasa->jumlah }}">
                                            </td>
                                        </form>
                                        <td>{{ number_format($jasa->subtotal, 2, ',', '.') }}</td>
                                        <td>
                                            <form
                                                action="{{ route('pesanan.jasa.destroy', ['jenis' => $pesanan->jenis, 'id' => $jasa->kd_quotation_item ?? $jasa->kd_invoice_item]) }}"
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
                        {{ number_format($billing_barang->sum('subtotal'), 2, ',', '.') }}<br>
                        <span class="font-weight-bold">Subtotal Jasa:</span> Rp.
                        {{ number_format($billing_jasa->sum('subtotal'), 2, ',', '.') }}<br>
                        <span class="font-weight-bold">Total:</span> Rp.
                        {{ number_format($billing_barang->sum('subtotal') + $billing_jasa->sum('subtotal'), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Pekerjaan</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <a class="btn btn-success" href="javascript:void(0)" id="createNewPekerjaan"> Tambah Pekerjaan</a>
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Signature</th>
                                <th width="180px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="pekerjaanForm" name="pekerjaanForm" class="form-horizontal">
                        <input type="hidden" name="kd_pekerjaan" id="kd_pekerjaan">
                        <input type="hidden" name="kd_tiket" id="kd_tiket" value={{ $tiket->kd_tiket }}>
                        <div class="form-group">
                            <label for="kd_karyawan" class="col-sm-4 control-label">Karyawan</label>
                            <div class="col-sm-12">
                                @php
                                    $karyawans = \App\Models\Karyawan::all();
                                    $karyawan_config = [
                                        "placeholder" => "Pilih satu atau lebih karyawan...",
                                        "allowClear" => true,
                                        "dropdownParent" => "#ajaxModel",
                                    ];
                                @endphp
                                <x-adminlte-select2 name="kd_karyawan[]" id="kd_karyawan" :config="$karyawan_config"
                                    multiple>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->kd_karyawan }}">{{ $karyawan->nama }}</option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tanggal" class="col-sm-4 control-label">Tanggal</label>
                            <div class="col-sm-12">
                                @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
                                <x-adminlte-input-date name="tanggal" id="tanggal" :config="$configDate"
                                    placeholder="Pilih tanggal..." igroup-size="md" required>
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                                    </x-slot>
                                </x-adminlte-input-date>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="jenis" class="col-sm-4 control-label">Jenis</label>
                            <div class="col-sm-12">
                                <x-adminlte-select2 name="jenis" id="jenis" :config="['dropdownParent' => '#ajaxModel','placeholder' => 'Masukan jenis...', 'tags' => true, 'allowClear' => true]" required>
                                    <option value="Kunjungan">Kunjungan</option>
                                    <option value="Cek di kantor">Cek di kantor</option>
                                    <option value="Perbaikan di kantor">Perbaikan di kantor</option>
                                    <option value="Remote">Remote</option>
                                </x-adminlte-select2>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keterangan_pekerjaan" class="col-sm-4 control-label">Keterangan Pekerjaan</label>
                            <div class="col-sm-12">
                                <textarea id="pekerjaan" name="keterangan_pekerjaan" required
                                    placeholder="Masukkan keterangan pekerjaan" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-12">
                                <x-adminlte-select name="status" id="status" required>
                                    <option value="Akan Dikerjakan">Akan Dikerjakan</option>
                                    <option value="Dalam Proses">Dalam Proses</option>
                                    <option value="Ditunda">Ditunda</option>
                                    <option value="Dilanjutkan">Dilanjutkan</option>
                                    <option value="Selesai">Selesai</option>
                                </x-adminlte-select>
                            </div>

                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                ajax: {
                    url: "{{ route('pekerjaan.index') }}",
                    type: "GET",
                    data: function (d) {
                        d.kd_tiket = "{{ $tiket->kd_tiket }}";
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'pelanggan', name: 'pelanggan.nama_pelanggan' },
                    { data: 'karyawan', name: 'karyawans.nama' },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'jenis', name: 'jenis' },
                    { data: 'keterangan', name: 'keterangan_pekerjaan' },
                    { data: 'status', name: 'status' },
                    { data: 'signature', name: 'signature_status', orderable: false, searchable: false }, 
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#createNewPekerjaan').click(function () {
                $('#saveBtn').val("create-pekerjaan");
                $('#saveBtn').html('Simpan');
                $('#kd_pekerjaan').val('');
                $('#pekerjaanForm').trigger("reset");
                $('#kd_karyawan').val(null).trigger('change');
                $('#modelHeading').html("Tambah Pekerjaan Baru");
                $('#ajaxModel').modal('show');
            });

            $('body').on('click', '.editPekerjaan', function () {
                var kd_pekerjaan = $(this).data('id');
                $.get("{{ route('pekerjaan.index') }}" + '/' + kd_pekerjaan + '/edit', function (data) {
                    $('#modelHeading').html("Edit Data Pekerjaan");
                    $('#saveBtn').val("edit-pekerjaan");
                    $('#saveBtn').html('Simpan Perubahan');
                    $('#ajaxModel').modal('show');
                    $('#kd_pekerjaan').val(data.kd_pekerjaan);
                    $('#kd_tiket').val(data.kd_tiket).trigger('change');
                    $('#tanggal').val(data.tanggal);
                    $('#jenis').val(data.jenis).trigger('change');
                    $('#pekerjaan').val(data.keterangan_pekerjaan);
                    $('#status').val(data.status).trigger('change');

                    var selectedKaryawanIds = data.karyawans.map(function (karyawan) {
                        return karyawan.kd_karyawan;
                    });
                    $('#kd_karyawan').val(selectedKaryawanIds).trigger('change');
                })
            });

            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Mengirim..');

                $.ajax({
                    data: $('#pekerjaanForm').serialize(),
                    url: "{{ route('pekerjaan.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#pekerjaanForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire('Sukses!', data.success, 'success');
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (data.responseJSON && data.responseJSON.errors) {
                            errorMsg = Object.values(data.responseJSON.errors).map(val => val.join('<br>')).join('<br>');
                        }
                        $('#saveBtn').html('Simpan');
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            });

            $('body').on('click', '.deletePekerjaan', function () {
                var kd_pekerjaan = $(this).data("id");
                var urlTemplate = "{{ route('pekerjaan.destroy', ':id') }}";
                var deleteUrl = urlTemplate.replace(':id', kd_pekerjaan);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: deleteUrl,
                            success: function (data) {
                                table.ajax.reload(null, false);
                                Swal.fire('Dihapus!', data.success, 'success');
                            },
                            error: function (data) {
                                console.log('Error:', data);
                                Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#suratPerintahTable, #barangTable').DataTable({
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
            $('#suratPerintahTable, #barangTable, #jasaTable').on('click', '.tombol-hapus', function (e) {
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
            document.querySelector('.select2-container--open .select2-search__field').focus();
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