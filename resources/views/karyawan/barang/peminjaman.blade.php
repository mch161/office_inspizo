@extends('adminlte::page')

@section('title', 'Peminjaman')

@section('content_header')
<h1>Peminjaman</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Peminjaman Barang</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-8">
                            <x-adminlte-select2 name="kd_barang" label="Barang" required>
                                <option class="text-muted" value="" selected disabled>Cari barang...</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->kd_barang }}">{{ $barang->nama_barang }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" placeholder="0"
                                required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Pinjam</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="dipinjamTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Karyawan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dipinjam as $peminjaman)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $peminjaman->barang->nama_barang }}</td>
                                <td>{{ $peminjaman->jumlah }}</td>
                                <td>
                                    @if ($peminjaman->status == '0')
                                        <span class="badge badge-warning">Dipinjam</span>
                                    @elseif ($peminjaman->status == '1')
                                        <span class="badge badge-success">Dikembalikan</span>
                                    @elseif ($peminjaman->status == '2')
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>{{ $peminjaman->karyawan->nama }}</td>
                                <td>
                                    @if ($peminjaman->status == '0')
                                        <form action="{{ route('peminjaman.update', $peminjaman->kd_peminjaman) }}"
                                            method="POST" style="display: inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i>
                                                Dikembalikan</button>
                                        </form>
                                        <form action="{{ route('peminjaman.update', $peminjaman->kd_peminjaman) }}"
                                            method="POST" style="display: inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="2">
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fas fa-undo"></i>
                                                Dibatalkan</button>
                                        </form>
                                    @endif
                                    @if ($peminjaman->status != '0')
                                        <form action="{{ route('peminjaman.destroy', $peminjaman->kd_peminjaman) }}"
                                            method="POST" style="display: inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm tombol-hapus"><i
                                                    class="fas fa-trash"></i>
                                                Hapus</button>
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
            $('#dipinjamTable').DataTable({
                scrollX: true,
                paging: false,
                scrollCollapse: true,
                scrollY: '200px',
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
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                })
            });
        })
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
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
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                })
            @endif
    </script>
@endsection