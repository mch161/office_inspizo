@extends('adminlte::page')

@section('title', 'Manajemen Pekerjaan')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
    <h1>Manajemen Pekerjaan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="javascript:void(0)" id="createNewPekerjaan"> Tambah Pekerjaan</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nama Karyawan</th>
                    <th>Pekerjaan</th>
                    <th width="180px">Action</th>
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
                <form id="pekerjaanForm" name="pekerjaanForm" class="form-horizontal">
                   <input type="hidden" name="kd_pekerjaan" id="kd_pekerjaan">

                    <div class="form-group">
                        <label for="kd_project" class="col-sm-4 control-label">Project</label>
                        <div class="col-sm-12">
                            <select name="kd_project" id="kd_project" class="form-control" required>
                                <option value="" disabled selected>Pilih Project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->kd_project }}">{{ $project->nama_project }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kd_karyawan" class="col-sm-4 control-label">Karyawan</label>
                        <div class="col-sm-12">
                            @php
                                $karyawan_config = [
                                    "placeholder" => "Pilih satu atau lebih karyawan...",
                                    "allowClear" => true,
                                    "dropdownParent" => "#ajaxModel",
                                ];
                            @endphp
                            <x-adminlte-select2 name="kd_karyawan[]" id="kd_karyawan" :config="$karyawan_config" multiple>
                                @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->kd_karyawan }}">{{ $karyawan->nama }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pekerjaan" class="col-sm-4 control-label">Deskripsi Pekerjaan</label>
                        <div class="col-sm-12">
                            <textarea id="pekerjaan" name="pekerjaan" required placeholder="Masukkan deskripsi pekerjaan" class="form-control" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
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
        ajax: "{{ route('pekerjaan.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'project', name: 'project.nama_project'},
            {data: 'karyawan', name: 'karyawans.nama'},
            {data: 'pekerjaan', name: 'pekerjaan'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
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
      $.get("{{ route('pekerjaan.index') }}" +'/' + kd_pekerjaan +'/edit', function (data) {
          $('#modelHeading').html("Edit Data Pekerjaan");
          $('#saveBtn').val("edit-pekerjaan");
          $('#saveBtn').html('Simpan Perubahan');
          $('#ajaxModel').modal('show');
          $('#kd_pekerjaan').val(data.kd_pekerjaan);
          $('#kd_project').val(data.kd_project);
          $('#pekerjaan').val(data.pekerjaan);

          var selectedKaryawanIds = data.karyawans.map(function(karyawan) {
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

    $('body').on('click', '.deletePekerjaan', function (){
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
</script>
@stop

