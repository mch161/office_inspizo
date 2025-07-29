@extends('adminlte::page')

@section('title', 'Customers')

@section('plugins.DataTables', true)
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1><i class="fas fa-users"></i> Customer Management</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <button id="add-customer-btn" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add New Customer</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th width="150px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Customer Modal -->
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
                    <form id="pelangganForm" name="pelangganForm" class="form-horizontal">
                       <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_pelanggan" class="col-sm-12 control-label">Customer Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder="Enter Name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama_perusahaan" class="col-sm-12 control-label">Company Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" placeholder="Enter Company Name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-12 control-label">Email</label>
                                    <div class="col-sm-12">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telp_pelanggan" class="col-sm-12 control-label">Phone Number</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" id="telp_pelanggan" name="telp_pelanggan" placeholder="Enter Phone Number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="alamat_pelanggan" class="col-sm-12 control-label">Address</label>
                                    <div class="col-sm-12">
                                        <textarea id="alamat_pelanggan" name="alamat_pelanggan" placeholder="Enter Address" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-sm-12 control-label">Password</label>
                                    <div class="col-sm-12">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep unchanged">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                         <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes</button>
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
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize DataTable
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pelanggan.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_pelanggan', name: 'nama_pelanggan'},
            {data: 'nama_perusahaan', name: 'nama_perusahaan'},
            {data: 'email', name: 'email'},
            {data: 'telp_pelanggan', name: 'telp_pelanggan'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
    // CREATE: Show modal for creating a new customer
    $('#add-customer-btn').click(function () {
        $('#saveBtn').val("create-pelanggan");
        $('#pelanggan_id').val('');
        $('#pelangganForm').trigger("reset");
        $('#modelHeading').html("Add New Customer");
        $('#password').attr('placeholder', 'Enter Password (Required)'); // Change placeholder
        $('#ajaxModel').modal('show');
    });
    
    // EDIT: Show modal and fetch customer data
    $('body').on('click', '.editPelanggan', function () {
      var pelanggan_id = $(this).data('id');
      $.get("{{ route('pelanggan.index') }}" +'/' + pelanggan_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Customer");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#pelanggan_id').val(data.kd_pelanggan);
          $('#nama_pelanggan').val(data.nama_pelanggan);
          $('#nama_perusahaan').val(data.nama_perusahaan);
          $('#email').val(data.email);
          $('#telp_pelanggan').val(data.telp_pelanggan);
          $('#alamat_pelanggan').val(data.alamat_pelanggan);
          $('#password').attr('placeholder', 'Leave blank to keep unchanged'); // Change placeholder
      })
   });
    
    // SAVE & UPDATE: Handle form submission
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
    
        $.ajax({
          data: $('#pelangganForm').serialize(),
          url: "{{ route('pelanggan.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              $('#pelangganForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
              Swal.fire('Success!', data.success, 'success');
              $('#saveBtn').html('Save Changes');
          },
          error: function (data) {
              console.log('Error:', data);
              var errors = data.responseJSON.errors;
              var errorMsg = '';
              $.each(errors, function(key, value){
                  errorMsg += value[0] + '<br>';
              });
              Swal.fire('Error!', errorMsg, 'error');
              $('#saveBtn').html('Save Changes');
          }
      });
    });
    
    // DELETE: Handle delete button click
    $('body').on('click', '.deletePelanggan', function () {
        var pelanggan_id = $(this).data("id");
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('pelanggan.store') }}"+'/'+pelanggan_id,
                    success: function (data) {
                        table.draw();
                        Swal.fire('Deleted!', data.success, 'success');
                    },
                    error: function (data) {
                        console.log('Error:', data);
                         Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@stop
