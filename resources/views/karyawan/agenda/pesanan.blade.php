@extends('adminlte::page')

@section('title', 'Orders')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Orders</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <button id="add-order-btn" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create New Order</button>
        </div>
        <div class="card-body">
            <table id="orders-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->kd_pesanan }}</td>
                            <td>{{ $pesanan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                            <td><span class="badge badge-primary">{{ $pesanan->status }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $pesanan->kd_pesanan }}"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">New Order</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="orderForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Customer</label>
                                <select name="kd_pelanggan" id="kd_pelanggan" class="form-control select2" style="width: 100%;">
                                    <option value="">Select Customer</option>
                                    @foreach($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->kd_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Baru">Baru</option>
                                    <option value="Proses">Proses</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Batal">Batal</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="deskripsi_pesanan" id="deskripsi_pesanan" class="form-control" rows="2"></textarea>
                        </div>
                        <hr>
                        <h5>Order Details</h5>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Item</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 20%;">Price</th>
                                    <th style="width: 20%;">Subtotal</th>
                                    <th><button type="button" class="btn btn-success btn-sm" id="add-item-row"><i class="fas fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody id="item-details-body">
                                </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.select2').select2({ dropdownParent: $('#orderModal') });

    // CREATE: Show modal
    $('#add-order-btn').click(function () {
        $('#orderForm').trigger("reset").find('select').val(null).trigger('change');
        $('#orderModalLabel').html("Create New Order");
        $('#item-details-body').html('');
        addItemRow();
        $('#orderModal').modal('show');
    });

    // VIEW: Show modal with data
    $('body').on('click', '.view-btn', function() {
        var order_id = $(this).data('id');
        $.get('/pesanan/' + order_id, function (data) {
            $('#orderModalLabel').html("View Order #" + data.kd_pesanan);
            $('#kd_pelanggan').val(data.kd_pelanggan).trigger('change');
            $('#deskripsi_pesanan').val(data.deskripsi_pesanan);
            $('#status').val(data.status);

            var detailsHtml = '';
            data.details.forEach(function(detail) {
                detailsHtml += `
                    <tr>
                        <td><input type="text" class="form-control" value="${detail.nama_barang}" readonly></td>
                        <td><input type="text" class="form-control" value="${detail.jumlah}" readonly></td>
                        <td><input type="text" class="form-control" value="${detail.harga_jual}" readonly></td>
                        <td><input type="text" class="form-control" value="${detail.subtotal}" readonly></td>
                        <td></td>
                    </tr>
                `;
            });
            $('#item-details-body').html(detailsHtml);
            $('#orderModal').modal('show');
        })
    });

    // Dynamically add a new row for items
    $('#add-item-row').click(addItemRow);

    function addItemRow() {
        var row = `
            <tr>
                <td>
                    <select class="form-control item-select" name="details[][kd_barang]">
                        <option value="">Select Item</option>
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->kd_barang }}" data-price="{{ $barang->harga }}" data-name="{{ $barang->nama_barang }}">{{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                     <input type="hidden" class="item-name" name="details[][nama_barang]">
                </td>
                <td><input type="number" class="form-control item-qty" name="details[][jumlah]" value="1" min="1"></td>
                <td><input type="number" class="form-control item-price" name="details[][harga_jual]"></td>
                <td><input type="text" class="form-control item-subtotal" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#item-details-body').append(row);
    }

    // Remove item row
    $('#item-details-body').on('click', '.remove-item-row', function() {
        $(this).closest('tr').remove();
    });
    
    // Auto-fill price and calculate subtotal
    function calculateRow(row) {
        var qty = parseFloat(row.find('.item-qty').val()) || 0;
        var price = parseFloat(row.find('.item-price').val()) || 0;
        row.find('.item-subtotal').val(qty * price);
    }

    $('#item-details-body').on('change', '.item-select', function() {
        var selected = $(this).find(':selected');
        var price = selected.data('price');
        var name = selected.data('name');
        var row = $(this).closest('tr');
        row.find('.item-price').val(price);
        row.find('.item-name').val(name); // Store the name in a hidden input
        calculateRow(row);
    });

    $('#item-details-body').on('input', '.item-qty, .item-price', function() {
        calculateRow($(this).closest('tr'));
    });

    // SAVE: Handle form submission
    $('#orderForm').submit(function(e) {
        e.preventDefault();
        $('#saveBtn').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('pesanan.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#orderModal').modal('hide');
                Swal.fire('Success!', response.success, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMsg = 'Please fix the following errors:<br>';
                $.each(errors, function(key, value) {
                    errorMsg += '- ' + value[0] + '<br>';
                });
                Swal.fire('Error!', errorMsg, 'error');
                $('#saveBtn').html('Create Order').prop('disabled', false);
            }
        });
    });
});
</script>
@stop