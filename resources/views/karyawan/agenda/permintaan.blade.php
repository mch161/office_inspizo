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
                            <td>{{ $pesanan->kd_pesanan }}</td>
                            <td>{{ $pesanan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                            <td>{{ $pesanan->tanggal}}</td>
                            <td class="text-center">
                                <span class="badge badge-warning">Menunggu</span>
                            </td>
                            <td>
                                <form action="{{ route('pesanan.accept', $pesanan->kd_pesanan) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm accept-btn  "><i
                                            class="fas fa-check"></i></button>
                                </form>
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

        $('#pesananTable').on('click', '.accept-btn', function (e) {
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