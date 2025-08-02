@extends('adminlte::page')

@section('title', 'Orders')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Pesanan</h1>
@stop

@section('content')
<div class="card card-primary card-outline">

    <div class="card-header">
        <x-adminlte-button label="Buat order baru" theme="primary" data-toggle="modal"
            data-target="#add-order-button" />
        <div class="card-body">
            <table id="orders-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Customer</th>
                        <th>Deskripsi Pesanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan as $pesanan)
                        <tr>
                            <td>{{ $pesanan->kd_pesanan }}</td>
                            <td>{{ $pesanan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                            <td>{{ $pesanan->tanggal}}</td>
                            <td class="text-center">
                                @if ($pesanan->status == 1)
                                    <span class="badge bg-success">Selesai</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 1)
                                    <span class="badge bg-info">Pesanan Dibuat</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 2)
                                    <span class="badge bg-warning">Pesanan Diterima</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 3)
                                    <span class="badge bg-secondary">Pesanan Diproses</span>
                                @elseif ($pesanan->status == 2)
                                    <span class="badge bg-danger">Batal</span>
                                @endif
                            </td>
                            <td>
                                @if ($pesanan->progres == 1)
                                    <form action="{{ route('pesanan.accept', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm accept-btn  "><i
                                                class="fas fa-check"></i></button>
                                    </form>
                                @else
                                    <a href="{{ route('pesanan.detail', $pesanan->kd_pesanan)}}"
                                        class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td><span>pesanan kosong</span></td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-adminlte-modal id="add-order-button" title="Buat order baru">
        <form id="orderForm" method="POST" action="{{ route('pesanan.store') }}">
            <label>Pilih customer</label>
            <select name="kd_pelanggan" id="kd_pelanggan">
                <option value="">Pilih Customer</option>
                @foreach($pelanggan as $pelanggan)
                    <option value="{{ $pelanggan->kd_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                @endforeach
            </select>
            @php $config = ['format' => 'YYYY-MM-DD']; @endphp
            <x-adminlte-input-date name="tanggal" value="{{ date('Y-m-d') }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal janji temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            <label>Description</label>
            <textarea name="deskripsi_pesanan" id="deskripsi_pesanan" class="form-control"></textarea>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Create Order</button>
            </div>
    </x-adminlte-modal>
    </form>
</div>
</div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('.select2').select2({ dropdownParent: $('#orderModal') });


        $('#orders-table').on('click', '.accept-btn', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Terima Pesanan',
                text: "Data yang di ganti tidak dapat dikembalikan!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Terima!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        $('#item-details-body').on('change', '.item-select', function () {
            var selected = $(this).find(':selected');
            var price = selected.data('price');
            var name = selected.data('name');
            var row = $(this).closest('tr');
            row.find('.item-price').val(price);
            row.find('.item-name').val(name); // Store the name in a hidden input
            calculateRow(row);
        });

        $('#item-details-body').on('input', '.item-qty, .item-price', function () {
            calculateRow($(this).closest('tr'));
        });

        // SAVE: Handle form submission
        $('#orderForm').submit(function (e) {
            e.preventDefault();
            $('#saveBtn').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            $.ajax({
                url: "{{ route('pesanan.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    $('#orderModal').modal('hide');
                    Swal.fire('Success!', response.success, 'success').then(() => location.reload());
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'Please fix the following errors:<br>';
                    $.each(errors, function (key, value) {
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