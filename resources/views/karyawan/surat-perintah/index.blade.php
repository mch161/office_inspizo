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
                            <th>Pesanan / Project</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
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
                                    @elseif ($item->project)
                                        <a href="{{ route('project.detail', $item->project->kd_project) }}">
                                            {{ $item->project->nama_project ?? '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    @if ($item->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} -
                                        {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == '0')
                                        <div class="badge badge-warning">Menunggu</div>
                                    @else
                                        <div class="badge badge-success">Selesai</div>
                                    @endif
                                </td>
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
                                    @if (Auth::guard('karyawan')->user()->kd_karyawan == $item->kd_karyawan && $item->status == '0')
                                        <form action="{{ route('surat-perintah.update', $item->kd_surat_perintah_kerja) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i>
                                                Tandai Selesai</button>
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
