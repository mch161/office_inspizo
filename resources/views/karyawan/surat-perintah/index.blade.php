@extends('adminlte::page')

@section('title', 'Surat Perintah Kerja')

@section('content_header')
<h1>Surat Perintah Kerja</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if (Auth::guard('karyawan')->user()->role == 'superadmin')
                    <a href="{{ route('surat-perintah.create') }}" class="btn btn-sm btn-primary mb-3 float-right"><i
                            class="fas fa-plus"></i> Tambah Surat Perintah Kerja</a>
                @endif
                <table id="suratPerintahTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            @if (Auth::guard('karyawan')->user()->role == 'superadmin')
                                <th>Karyawan</th>
                            @endif
                            <th>Pesanan</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surat_perintah as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @if (Auth::guard('karyawan')->user()->role == 'superadmin')
                                    <td>{{ $item->karyawan->nama }}</td>
                                @endif
                                <td>
                                    @if ($item->pesanan)
                                        <a href="{{ route('pesanan.detail', $item->pesanan->kd_pesanan) }}">
                                            {{ $item->pesanan->deskripsi_pesanan ?? '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td>{{ $item->tanggal_mulai }}</td>
                                <td>
                                    @if (Auth::guard('karyawan')->user()->role == 'superadmin')
                                        <form action="{{ route('surat-perintah.destroy', $item->kd_surat_perintah_kerja) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger tombol-hapus"><i
                                                    class="fas fa-trash"></i> Hapus</button>
                                        </form>
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
            $('#suratPerintahTable').DataTable({
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
            $('.tombol-hapus').on('click', function (e) {
                let form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            })
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
@endsection