@extends('adminlte::page')

@section('title', 'Tiket')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Tiket</h1>
@stop

@section('content')
<div class="card card-primary card-outline mb-3">
    <div class="card-header">
        <a href="javascript:void(0)" id="createNewPesanan" class="btn btn-success"> Tambah Tiket</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped pesanan-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Nama Pelanggan</th>
                    <th>Deskripsi</th>
                    <th>Prioritas</th>
                    <th width=5%">Jenis</th>
                    <th>Tanggal</th>
                    <th>Via</th>
                    <th width="100px">Status</th>
                    <th width="170px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Permintaan</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped permintaan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<x-adminlte-modal id="agendaModal" title="Agendakan Pesanan" theme="primary">
    <form id="agendaForm" method="POST" action="{{ route('tiket.agenda') }}">
        @csrf
        <input type="hidden" name="kd_pesanan" id="kd_pesanan">
        <label for="title">Nama</label>
        <input class="form-control" type="text" name="title" id="title" placeholder="Nama Agenda" required>

        @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
        <x-adminlte-input-date name="tanggal" id="tanggal-agenda2" :config="$configDate" placeholder="Pilih tanggal..."
            label="Tanggal Janji Temu" igroup-size="md" required>
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
            <button type="submit" class="btn btn-primary" id="saveBtn" form="agendaForm">Simpan</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
        </x-slot>
    </form>
</x-adminlte-modal>

<div class="modal fade" id="pesananModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="pesananForm" name="pesananForm" class="form-horizontal">
                    <input type="hidden" name="kd_pesanan" id="form_kd_pesanan">
                    <x-adminlte-select2 id="kd_pelanggan" name="kd_pelanggan" label="Pelanggan">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-lg fa-user"></i>
                            </div>
                        </x-slot>
                        <option value="" disabled selected>Pilih pelanggan...</option>
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->kd_pelanggan }}">{{ $item->nama_pelanggan }}</option>
                        @endforeach
                    </x-adminlte-select2>
                    @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
                    <x-adminlte-input-date name="tanggal" id="tanggal-agenda" :config="$configDate"
                        placeholder="Pilih tanggal..." label="Tanggal Janji Temu" igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>

                    <x-adminlte-select name="prioritas" id="prioritas" label="Prioritas">
                        <option value="1">Normal</option>
                        <option value="2">Segera</option>
                        <option value="3">Penting</option>
                    </x-adminlte-select>

                    <x-adminlte-select2 name="jenis" id="jenis" :config="['placeholder' => 'Masukan jenis...', 'tags' => true, 'allowClear' => true]" label="Jenis">
                        <option value="" disabled selected>Pilih jenis...</option>
                        <option value="Pasang Baru">Pasang Baru</option>
                        <option value="Pesanan">Pesanan</option>
                        <option value="Perbaiki">Perbaiki</option>
                    </x-adminlte-select2>

                    <x-adminlte-select2 name="via" id="via" :config="['placeholder' => 'Masukan via...', 'tags' => true, 'allowClear' => true]" label="Via">
                        <option value="" disabled selected>Pilih via...</option>
                        <option value="Telepon">Telepon</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Web">Web</option>
                    </x-adminlte-select2>

                    <label>Deskripsi</label>
                    <textarea name="deskripsi_pesanan" id="deskripsi_pesanan" class="form-control"></textarea>

                    <div class="col-sm-offset-2 col-sm-10 mt-3">
                        <button type="button" class="btn btn-success" id="saveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var table = $('.pesanan-table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [[5, "desc"]],
            ajax: "{{ route('tiket.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_pelanggan', name: 'pelanggan.nama_pelanggan' },
                { data: 'deskripsi_pesanan', name: 'tiket.deskripsi' },
                { data: 'prioritas', name: 'tiket.prioritas' },
                { data: 'jenis', name: 'pesanan.jenis' },
                { data: 'tanggal', name: 'tiket.tanggal' },
                { data: 'via', name: 'tiket.via' },
                { data: 'status', name: 'pesanan.progress' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        var table_permintaan = $('.permintaan-table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "{{ route('tiket.permintaan') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_pelanggan', name: 'pesanan.nama_pelanggan' },
                { data: 'deskripsi_pesanan', name: 'pesanan.deskripsi_pesanan' },
                { data: 'tanggal', name: 'pesanan.tanggal' },
                { data: 'status', name: 'pesanan.status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#createNewPesanan').click(function () {
            $('#pesananForm').trigger("reset");
            $('#myModalLabel').html("Tambah Pesanan Baru");
            $('#saveBtn').html('Simpan');
            $('#kd_pelanggan').val('').trigger('change');
            $('#prioritas').val('').trigger('change');
            $('#jenis').val('').trigger('change');
            $('#via').val('').trigger('change');
            $('#form_kd_pesanan').val('');
            $('#pesananModal').modal('show');
        });

        $('body').on('click', '.editBtn', function () {
            var kd_pesanan = $(this).data('id');
            $.get("{{ route('tiket.index') }}" + '/' + kd_pesanan + '/edit', function (data) {
                $('#myModalLabel').html("Edit Data Pesanan");
                $('#saveBtn').html('Simpan Perubahan');
                $('#pesananModal').modal('show');
                $('#kd_pelanggan').val(data.kd_pelanggan).trigger('change');
                $('#deskripsi_pesanan').val(data.deskripsi_pesanan);
                $('#tanggal-agenda').val(data.tanggal);
                
                tiket = data.tiket;
                $('#prioritas').val(tiket.prioritas).trigger('change');
                $('#jenis').val(tiket.jenis).trigger('change');
                $('#via').val(tiket.via).trigger('change');

                $('#form_kd_pesanan').val(data.kd_pesanan);
            })
        });

        $('#pesananModal').on('click', '#saveBtn', function (e) {
            e.preventDefault();
            var saveButton = $(this);
            saveButton.html('Menyimpan...');

            $.ajax({
                data: $('#pesananForm').serialize(),
                url: "{{ route('tiket.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#pesananForm').trigger("reset");
                    $('#pesananModal').modal('hide');
                    table.ajax.reload(null, false);
                    table_permintaan.ajax.reload(null, false);
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

        $('body').on('click', '.agendaBtn', function () {
            var kd_pesanan = $(this).data('id');
            $('#agendaModal #kd_pesanan').val(kd_pesanan);
            $('#agendaModal').modal('show');
        });

        $('body').on('click', '.accept-btn', function (e) {
            e.preventDefault();
            var kd_pesanan = $(this).data('id');
            var url = "{{ route('tiket.index') }}" + '/' + kd_pesanan;

            Swal.fire({
                title: 'Terima pesanan ini?',
                text: "Pesanan akan dipindahkan ke daftar 'Pesanan'.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, terima!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            progres: 2
                        },
                        success: function (data) {
                            table.ajax.reload(null, false);
                            table_permintaan.ajax.reload(null, false);
                            Swal.fire('Diterima!', data.success || 'Pesanan telah diterima.', 'success');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            Swal.fire('Error!', 'Gagal menerima pesanan.', 'error');
                        }
                    });
                }
            });
        });

        $('body').on('click', '.batalkan-btn', function (e) {
            e.preventDefault();
            var kd_pesanan = $(this).data('id');
            var url = "{{ route('tiket.index') }}" + '/' + kd_pesanan;

            Swal.fire({
                title: 'Batalkan pesanan ini?',
                text: "Status pesanan akan diubah menjadi 'Dibatalkan'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Nanti dulu'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            status: 2
                        },
                        success: function (data) {
                            table.ajax.reload(null, false);
                            table_permintaan.ajax.reload(null, false);
                            Swal.fire('Dibatalkan!', data.success || 'Pesanan telah dibatalkan.', 'success');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            Swal.fire('Error!', 'Gagal membatalkan pesanan.', 'error');
                        }
                    });
                }
            });
        });

        $('body').on('click', '.deleteBtn', function (e) {
            e.preventDefault();
            var kd_pesanan = $(this).data('id');
            var url = "{{ route('tiket.index') }}" + '/' + kd_pesanan;

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
                            table_permintaan.ajax.reload(null, false);
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
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                })
            @endif
    });
</script>
@stop