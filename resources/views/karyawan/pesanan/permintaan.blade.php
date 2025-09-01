@extends('adminlte::page')

@section('title', 'Pesanan')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Daftar Permintaan</h1>
@stop

@section('content')
<div class="card card-primary card-outline">

    <div class="card-header">
        <div class="card-body">
            <table id="pesananTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Deskripsi Pesanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan as $pesanan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pesanan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                            <td>{{ $pesanan->tanggal}}</td>
                            <td class="text-center">
                                @if ($pesanan->status == '2')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if ($pesanan->status == '0')
                                    <form action="{{ route('pesanan.update', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="progres" value="2">
                                        <button type="submit" class="btn btn-success btn-sm accept-btn  "><i
                                                class="fas fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('pesanan.update', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="btn btn-danger btn-sm batalkan-btn"><i
                                                class="fas fa-times"></i></button>
                                    </form>
                                @endif
                                @if ($pesanan->status == '2')
                                    <form action="{{ route('pesanan.destroy', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm hapus-btn"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                    @empty
                            <tr>
                                <td colspan="6" class="text-center"><span>Tidak ada data</span></td>
                            </tr>
                        @endforelse
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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

        $('#pesananTable').on('click', '.accept-btn, .batalkan-btn, .hapus-btn', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let title = $(this).hasClass('accept-btn') ? 'Terima Pesanan' : ($(this).hasClass('batalkan-btn') ? 'Batalkan Pesanan' : 'Hapus Pesanan');
            let text = $(this).hasClass('hapus-btn') ? 'Data yang dihapus tidak dapat dikembalikan!' : 'Data yang di ganti tidak dapat dikembalikan!';
            let icon = $(this).hasClass('hapus-btn') ? 'warning' : 'question';
            let confirmButtonText = $(this).hasClass('accept-btn') ? 'Ya, Terima!' : ($(this).hasClass('batalkan-btn') ? 'Ya, Batalkan!' : 'Ya, Hapus!');
            let cancelButtonText = 'Batal';
            let confirmButtonColor = $(this).hasClass('accept-btn') ? '#28a745' : ($(this).hasClass('batalkan-btn') ? '#3085d6' : '#d33');
            let cancelButtonColor = $(this).hasClass('hapus-btn') ? '#28a745' : '#d33';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: cancelButtonColor,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
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