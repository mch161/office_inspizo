@extends('adminlte::page')

@section('title', 'Data Kunjungan')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1>Data Kunjungan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('kunjungan.create') }}" class="btn btn-sm btn-primary mb-3 float-right"><i
                        class="fas fa-plus"></i> Tambah Kunjungan</a>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Karyawan</th>
                            <th>Pelanggan</th>
                            <th>Pesanan</th>
                            <th>Keterangan</th>
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
    </div>
</div>
@stop

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
            ajax: "{{ route('kunjungan.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'karyawan', name: 'karyawans.nama', orderable: false },
                { data: 'pelanggan', name: 'pelanggan.nama_pelanggan' },
                { data: 'pesanan', name: 'pesanan.deskripsi_pesanan' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('.data-table tbody').on('click', '.tombol-selesai', function (e) {
            e.preventDefault();

            const updateUrl = $(this).data('url');
            const kunjunganId = $(this).data('id');
            const button = $(this);

            $.ajax({
                url: updateUrl,
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    '_method': 'PATCH'
                },
                success: function (response) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    Toast.fire({ icon: 'success', text: response.success });

                    table.ajax.reload();

                    button.fadeOut('slow', function () {
                        $(this).remove();
                    });
                },
                error: function (xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memperbarui status.', 'error');
                }
            });
        });

        $('.data-table tbody').on('click', '.tombol-hapus', function (e) {
            e.preventDefault();

            const deleteUrl = $(this).data('url');
            const row = $(this).closest('tr');

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
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire(
                                'Dihapus!',
                                response.success,
                                'success'
                            );

                            table.row(row).remove().draw();
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });

    @if (session()->has('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
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
</script>
@stop