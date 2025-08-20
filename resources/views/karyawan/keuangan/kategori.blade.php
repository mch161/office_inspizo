@extends('adminlte::page')

@section('title', 'Keuangan | Kategori')

@section('content_header')
<h1>Kategori</h1>
@stop

@section('css')

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST" id="kategoriForm" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nama_kategori">Nama kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama" required>
            </div>
            <x-adminlte-button theme="primary" icon="fas fa-plus" label="Tambahkan" type="submit" form="kategoriForm" />
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <x-adminlte-modal id="modalEdit" title="Edit Kategori" theme="primary" icon="fas fa-edit" size='lg'>
            <form method="POST" id="form-edit-kategori" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama_kategori_edit">Nama kategori</label>
                    <input type="text" class="form-control" id="nama_kategori_edit" name="nama" required>
                </div>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="primary" label="Simpan Perubahan" type="submit"
                        form="form-edit-kategori" />
                    <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
                </x-slot>
            </form>
        </x-adminlte-modal>

        <table id="kategoriTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Kategori</th>
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th width="150px" rigth>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategori as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k->nama }}</td>
                        <td>Rp {{ number_format($keuangan->where('kd_kategori', $k->kd_kategori)->sum('masuk'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($keuangan->where('kd_kategori', $k->kd_kategori)->sum('keluar'), 0, ',', '.') }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                                data-id="{{ $k->kd_kategori }}" data-nama_kategori="{{ $k->nama }}">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <form action="{{ route('kategori.destroy', $k->kd_kategori) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                                    <i class="fas fa-trash"></i>
                                    Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#kategoriTable').DataTable({
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

            $('.tombol-edit').on('click', function () {
                const id = $(this).data('id');
                const nama_kategori = $(this).data('nama_kategori');

                $('#nama_kategori_edit').val(nama_kategori);

                let form = $('#form-edit-kategori');
                let updateUrl = "{{ url('kategori') }}/" + id;
                form.attr('action', updateUrl);
            });

            @if ($errors->any() && session('invalid_kategori'))
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
                    text: '{{ session('invalid_kategori') }}',
                })
                const errorkategoriId = "{{ session('error_kategori_id') }}";

                if (errorkategoriId) {
                    let form = $('#form-edit-kategori');
                    let updateUrl = "{{ url('kategori') }}/" + errorkategoriId;
                    form.attr('action', updateUrl);
                }

                $('#modalEdit').modal('show');
            @endif

            $('#kategoriTable').on('click', '.tombol-hapus', function (e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
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
        });
    </script>
@endsection