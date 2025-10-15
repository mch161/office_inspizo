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
                <table id="kunjunganTable" class="table table-bordered">
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
                        @foreach ($kunjungan as $kunjungan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $kunjungan->karyawans->pluck('nama')->implode(', ') }}
                                </td>
                                <td>{{ $kunjungan->pelanggan->nama_pelanggan }}</td>
                                <td>{{ $kunjungan->pesanan->deskripsi_pesanan ?? '-' }}</td>
                                <td>{!! $kunjungan->keterangan ?? '-' !!}</td>
                                <td>{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d-m-Y') }}</td>
                                <td id="status-{{ $kunjungan->kd_kunjungan }}">
                                    @if ($kunjungan->status == '0')
                                        <span class="badge badge-warning">Belum Selesai</span>
                                    @else
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($kunjungan->karyawans->contains(Auth::user()->kd_karyawan))
                                        @if ($kunjungan->status == '0')
                                            <a href="javascript:void(0)" class="btn btn-sm btn-success tombol-selesai"
                                                data-url="{{ route('kunjungan.updateStatus', $kunjungan->kd_kunjungan) }}"
                                                data-id="{{ $kunjungan->kd_kunjungan }}">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('kunjungan.edit', $kunjungan->kd_kunjungan) }}"
                                            class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger tombol-hapus"
                                            data-url="{{ route('kunjungan.destroy', $kunjungan->kd_kunjungan) }}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#kunjunganTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
            scrollX: true,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                searchPlaceholder: "Cari data..."
            }
        });
        $('#kunjunganTable').on('click', '.tombol-selesai', function (e) {
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

                    $('#status-' + kunjunganId).html('<span class="badge badge-success">Selesai</span>');

                    button.fadeOut('slow', function () {
                        $(this).remove();
                    });
                },
                error: function (xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memperbarui status.', 'error');
                }
            });
        });
        $('#kunjunganTable').on('click', '.tombol-hapus', function (e) {
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
                            row.fadeOut('slow', function () {
                                $(this).remove();
                            });

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