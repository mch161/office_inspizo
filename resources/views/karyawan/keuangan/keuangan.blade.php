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
    <x-adminlte-modal id="modalTambah" title="Tambahkan Histori Keuangan" theme="success" icon="fas fa-clipboard" size='lg'>
        <form action="{{ route('keuangan.store') }}" method="POST" id="keuanganForm">
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

            <x-adminlte-input name="jumlah pemasukan" label="Jumlah" placeholder="Masukkan jumlah..." igroup-size="md" required>
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-select name="kotak" label="Kotak">
                <x-adminlte-options :options="$kotak->pluck('nama', 'kd_kotak')->toArray()" empty-option="Select an option..." />
            </x-adminlte-select>

            <x-adminlte-textarea name="isi_jurnal" label="Keterangan" rows=5 igroup-size="sm"
                placeholder="Tuliskan isi jurnal di sini..." required>
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-lg fa-file-alt text-warning"></i>
                    </div>
                </x-slot>
            </x-adminlte-textarea>

            <x-slot name="footerSlot">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="keuanganForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </x-slot>
        </form>
    </x-adminlte-modal>
    <table id="KeuanganTable" class="table table-bordered table-striped">
        <thead>
            <tr class="table-primary">
                <th width="5%">No</th>
                <th>Jenis</th>
                <th>Kotak</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th width="150px" rigth>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keuangans as $keuangan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $keuangan->jenis }}</td>
                    <td>{{ $keuangan->kotak->nama }}</td>
                    <td>{{ $keuangan->kategori }}</td>
                    <td>{{ $keuangan->keterangan }}</td>
                    <td>{{ $keuangan->created_at }}</td>
                    <td>{{ $keuangan->status }}</td>
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
            $('#KeuanganTable').DataTable({
                scrollX: true
            });
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
