@extends('adminlte::page')

@section('title', 'Hari Libur')

@section('content_header')
<h1>Hari Libur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Libur</h3>
    </div>
    <div class="card-body">
        <form id="LiburForm" action="{{ route('libur.store') }}" method="POST">
            @csrf
            @php $config = ['format' => 'DD-MM-YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" value="{{ date('d-m-Y') }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-select name="jenis_libur" label="Jenis Libur" igroup-size="md">
                <option value="" selected disabled>Pilih Jenis...</option>
                <option value="Nasional">Nasional</option>
                <option value="Cuti Bersama">Cuti Bersama</option>
                <option value="Internal">Internal</option>
            </x-adminlte-select>
            <x-adminlte-input name="keterangan" label="Keterangan" placeholder="Masukkan keterangan..."
                igroup-size="md" />
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table id="LiburTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis Libur</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($liburs as $libur)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($libur->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $libur->jenis_libur }}</td>
                        <td>{{ $libur->keterangan }}</td>
                        <td>
                            <form action="{{ route('libur.destroy', $libur->kd_presensi_libur) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger tombol-hapus"><i
                                        class="fas fa-trash"></i></button>
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
            $('#LiburTable').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                pageLength: -1,
                scrollX: true,
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ada data",
                    emptyTable: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Cari:",
                    searchPlaceholder: "Cari data..."
                }
            });
            $('#LiburForm').on('submit', function (event) {
                event.preventDefault();

                let jenisLibur = $('#jenis_libur').val();
                let keterangan = $('#keterangan').val();
                if (jenisLibur) {
                    $('#jenis_libur').removeClass('is-invalid');
                }
                if (keterangan) {
                    $('#keterangan').removeClass('is-invalid');
                }

                if (!jenisLibur) {
                    $('#jenis_libur').addClass('is-invalid');
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
                    })

                    Toast.fire({
                        icon: 'error',
                        title: 'Silakan pilih Jenis Libur terlebih dahulu!'
                    });
                    return;
                }

                if (!keterangan) {
                    $('#keterangan').addClass('is-invalid');
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
                    })

                    Toast.fire({
                        icon: 'error',
                        title: 'Silakan masukkan Keterangan terlebih dahulu!'
                    });
                    return;
                }

                this.submit();
            });
            $('#LiburTable').on('click', '.tombol-hapus', function (event) {
                event.preventDefault();
                console.log($(this).closest('form'));
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        })
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