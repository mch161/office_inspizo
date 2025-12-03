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
                        <a href="{{ $backUrl }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
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
                    <x-adminlte-button label="Tambahkan barang" class="mb-2 bg-blue" data-toggle="modal"
                        data-target="#barangModal" />
                    <table id="barangTable" class="table table-centered table-nowrap mb-0 rounded">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-adminlte-modal id="barangModal" title="Tambahkan barang" theme="primary">
        <form id="barangForm" method="POST">
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
                <x-adminlte-button theme="success" id="saveBtn" label="Simpan" type="submit" form="barangForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
@endsection

@section('js')
<script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        var table = $('#barangTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pekerjaan.show', $pekerjaan->kd_pekerjaan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_barang', name: 'nama_barang' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'aksi', name: 'aksi' },
            ]
        });
        $('#barangModal').on('click', '#saveBtn', function (e) {
            e.preventDefault();
            var saveButton = $(this);
            saveButton.html('Menyimpan...');

            $.ajax({
                data: $('#barangForm').serialize(),
                url: "{{ route('pekerjaan.barang.store', $pekerjaan->kd_pekerjaan) }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#barangForm').trigger("reset");
                    $('#barangModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire('Sukses!', data.success, 'success');
                },
                error: function (data) {
                    console.log('Error:', data);
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (data.responseJSON && data.responseJSON.errors) {
                        errorMsg = Object.values(data.responseJSON.errors).map(val => val.join('<br>')).join('<br>');
                    }
                    saveButton.html('Simpan');
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        });
        $('body').on('click', '.deleteBtn', function (e) {
            e.preventDefault();
            var kd_pekerajan = $(this).data('id');
            var url = "{{ route('pekerjaan.barang') }}" + '/' + kd_pekerajan;

            Swal.fire({
                title: 'Anda yakin?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function (data) {
                            table.ajax.reload(null, false);
                            Swal.fire('Dihapus!', data.success || 'Data berhasil dihapus.', 'success');
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
</script>
@stop