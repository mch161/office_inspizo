@extends('adminlte::page')

@section('title', 'Keuangan')

@section('content_header')
    <h1>Keuangan</h1>
    <div class="d-flex justify-content-end">
        <x-adminlte-button label="Tambahkan" class="mb-2 bg-blue" data-toggle="modal" data-target="#modalTambah" />
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('content')
    <x-adminlte-modal id="modalTambah" title="Tambahkan Jurnal" theme="success" icon="fas fa-clipboard" size='lg'>
        <form action="{{ route('keuangan.store') }}" method="POST" id="jurnalForm">
            @csrf
            {{-- Date picker --}}
            @php
                $config = ['format' => 'YYYY-MM-DD'];
            @endphp
            <x-adminlte-input-date name="tanggal" :config="$config" placeholder="Pilih tanggal..." label="Tanggal"
                igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>

            


            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="jurnalForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    <table id="KeuanganTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Kotak</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
                <th width="150px">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keuangans as $keuangan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $keuangan->jenis }}</td>
                    <td>{{ $keuangan->status }}</td>
                    <td>{{ $keuangan->masuk }}</td>
                    <td>{{ $keuangan->keluar }}</td>
                    <td>{{ $keuangan->kotak }}</td>
                    <td>{{ $keuangan->kategori }}</td>
                    <td>{{ $keuangan->keterangan }}</td>
                    <td>{{ $keuangan->created_at }}</td>
                    <td>
                        <form action="{{ route('keuangan.destroy', $keuangan->kd_keuangan) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#KeuanganTable').DataTable();
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
