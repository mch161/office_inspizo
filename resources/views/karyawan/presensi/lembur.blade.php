@extends('adminlte::page')

@section('title', 'Lembur')

@section('content_header')
<h1>Lembur</h1>
@stop

@section('content')
<x-adminlte-modal id="FormModal" icon="fas fa-clipboard" title="Form Lembur" theme="primary" size="lg">
    <form id="lemburForm" action="{{ route('lembur.store') }}" method="POST">
        @csrf
        @php $config = ['format' => 'DD-MM-YYYY']; @endphp
        <x-adminlte-input-date name="tanggal" value="{{ date('d-m-Y') }}" :config="$config"
            placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" required>
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
            </x-slot>
        </x-adminlte-input-date>
        @php $config = ['format' => 'HH:mm']; @endphp
        <x-adminlte-input-date name="jam_mulai" id="mulai" :config="$config" placeholder="Pilih jam..."
            label="Jam Mulai" igroup-size="md" required>
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
            </x-slot>
        </x-adminlte-input-date>
        @php $config = ['format' => 'HH:mm']; @endphp
        <x-adminlte-input-date name="jam_selesai" id="selesai" :config="$config" placeholder="Pilih jam..."
            label="Jam Selesai" igroup-size="md" required>
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
            </x-slot>
        </x-adminlte-input-date>
        <x-adminlte-textarea name="keterangan" label="Keterangan" rows=5 igroup-size="sm"
            placeholder="Masukkan keterangan...">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-lg fa-file-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Kirim" type="submit" form="lemburForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tabel Lembur</h3>
    </div>
    <div class="card-body">
        <div class="mb-3 float-right">
            <x-adminlte-button label="Form Lembur" icon="fas fa-clipboard" class="float-right mb-2 bg-blue" data-toggle="modal"
                data-target="#FormModal" />
        </div>
        <table id="LemburTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lemburs as $lembur)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $lembur->dibuat_oleh }}</td>
                        <td>{{ \Carbon\Carbon::parse($lembur->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }} ({{ $lembur->jumlah_jam }})</td>
                        <td>{{ $lembur->keterangan }}</td>
                        <td>
                            @if ($lembur->verifikasi == '0')
                                <div class="badge badge-warning">Menunggu</div>
                            @else
                                <div class="badge badge-success">Disetujui</div>
                            @endif
                        </td>
                        <td>
                            @if ($lembur->verifikasi == '0' && Auth::user()->role == 'superadmin')
                                <form action="{{ route('lembur.approve') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="kd_lembur" value="{{ $lembur->kd_lembur }}">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
                                </form>
                            @endif
                            @if ($lembur->verifikasi == '0' && $lembur->dibuat_oleh == auth()->user()->nama)
                                <form action="{{ route('lembur.destroy', $lembur->kd_lembur) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
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
        $('#LemburTable').DataTable({
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