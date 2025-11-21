@extends('adminlte::page')

@section('title', 'Manajemen Tugas')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)

@section('content_header')
<h1>Manajemen Tugas Harian</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="javascript:void(0)" id="createNewTugas"> Tambah Tugas</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <!-- <th>Project</th> -->
                    <th>Pekerjaan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th width="150px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
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
                <form id="tugasForm" name="tugasForm" class="form-horizontal">
                    <input type="hidden" name="kd_tugas" id="kd_tugas">

                    <x-adminlte-select2 name="kd_pekerjaan" id="kd_pekerjaan" label="Pekerjaan" required>
                        <option value="" disabled selected>Pilih Pekerjaan</option>
                        @foreach($pekerjaans as $p)
                            <option value="{{ $p->kd_pekerjaan }}">
                                {{ $p->keterangan_pekerjaan }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>

                    <div id="tanggal-form">
                        @php $configDate = ['format' => 'DD-MM-YYYY']; @endphp
                        <x-adminlte-input-date name="tanggal" id="tanggal" :config="$configDate" label="Tanggal Tugas">
                            <x-slot name="appendSlot">
                                <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>


                    <x-adminlte-select name="status" id="status" label="Status" required>
                        <option value="Akan Dikerjakan">Akan Dikerjakan</option>
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Ditunda">Ditunda</option>
                        <option value="Dilanjutkan">Dilanjutkan</option>
                        <option value="Selesai">Selesai</option>
                    </x-adminlte-select>

                    <div class="col-sm-offset-2 col-sm-10 mt-3">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tugas.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                // { data: 'project', name: 'pekerjaan.project.nama_project' },
                { data: 'pekerjaan_info', name: 'pekerjaan.pekerjaan' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#createNewTugas').click(function () {
            $('#saveBtn').val("create-tugas");
            $('#saveBtn').html('Simpan');
            $('#kd_tugas').val('');
            $('#tanggal-form').hide();
            $('#tugasForm').trigger("reset");
            $('#kd_pekerjaan, #kd_karyawan').val(null).trigger('change');
            $('#modelHeading').html("Tambah Tugas Baru");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editTugas', function () {
            var kd_tugas = $(this).data('id');
            $.get("{{ route('tugas.index') }}" + '/' + kd_tugas + '/edit', function (data) {
                $('#modelHeading').html("Edit Data Tugas");
                $('#saveBtn').html('Simpan Perubahan');
                $('#tanggal-form').show();
                $('#ajaxModel').modal('show');
                $('#kd_tugas').val(data.kd_tugas);
                $('#kd_pekerjaan').val(data.kd_pekerjaan).trigger('change');
                $('#kd_karyawan').val(data.kd_karyawan).trigger('change');
                $('#tanggal').val(data.tanggal);
                $('#status').val(data.status).trigger('change');
            })
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Mengirim..');

            $.ajax({
                data: $('#tugasForm').serialize(),
                url: "{{ route('tugas.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#tugasForm').trigger("reset");
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

        $('body').on('click', '.deleteTugas', function () {
            var kd_tugas = $(this).data("id");
            var urlTemplate = "{{ route('tugas.destroy', ':id') }}";
            var deleteUrl = urlTemplate.replace(':id', kd_tugas);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
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
                        url: deleteUrl,
                        success: function (data) {
                            table.ajax.reload(null, false);
                            Swal.fire('Dihapus!', data.success, 'success');
                        },
                        error: function (data) {
                            Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@stop