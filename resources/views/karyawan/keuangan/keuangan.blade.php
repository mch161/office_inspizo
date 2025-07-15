@extends('adminlte::page')

@section('title', 'Keuangan')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1>Keuangan</h1>


@stop

@section('css')
 
@endsection

@section('content')
<x-adminlte-modal id="modalMasuk" title="Tambahkan Histori Keuangan" theme="success" icon="fas fa-clipboard" size='lg'>
    <form action="{{ route('keuangan.store') }}" method="POST" id="modalMasukForm">
        @csrf
        <input type="hidden" name="jenis" value="Masuk">
        @php
            $config = ['format' => 'YYYY-MM-DD'];
        @endphp
        <x-adminlte-input-date id="tanggal" value="{{ date('Y-m-d') }}" name="tanggal" :config="$config"
            placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" required>
            <x-slot name="appendSlot">
                <div class="input-group-text">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-input-date>

        <x-adminlte-input name="masuk" label="Uang Masuk" placeholder="Masukkan jumlah..." igroup-size="md" required>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-money-bill"></i>
                </div>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-select name="kotak" label="Kotak">
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-credit-card"></i>
                </div>
            </x-slot>
            <x-adminlte-options :options="$kotak->pluck('nama', 'kd_kotak')->toArray()" empty-option="Pilih kotak..." />
        </x-adminlte-select>

        <x-adminlte-select name="kategori" label="Kategori">
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-wallet"></i>
                </div>
            </x-slot>
            <x-adminlte-options :options="$kategori->pluck('nama', 'kd_kategori')->toArray()"
                empty-option="Pilih kategori..." />
        </x-adminlte-select>

        <x-adminlte-textarea name="keterangan" label="Keterangan" rows=5 igroup-size="sm"
            placeholder="Tuliskan isi keterangan di sini..." required>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-file-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="modalMasukForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>
<x-adminlte-modal id="modalKeluar" title="Tambahkan Histori Keuangan" theme="success" icon="fas fa-clipboard" size='lg'>
    <form action="{{ route('keuangan.store') }}" method="POST" id="modalKeluarForm">
        @csrf
        <input type="hidden" name="jenis" value="Keluar">
        @php
            $config2 = ['format' => 'YYYY-MM-DD'];
        @endphp
        <x-adminlte-input-date id="tanggal2" value="{{ date('Y-m-d') }}" name="tanggal" :config="$config2"
            placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" required>
            <x-slot name="appendSlot">
                <div class="input-group-text">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-input-date>

        <x-adminlte-input name="keluar" label="Uang Keluar" placeholder="Masukkan jumlah..." igroup-size="md" required>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-money-bill"></i>
                </div>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-select name="kotak" label="Kotak">
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-credit-card"></i>
                </div>
            </x-slot>
            <x-adminlte-options :options="$kotak->pluck('nama', 'kd_kotak')->toArray()" empty-option="Pilih kotak..." />
        </x-adminlte-select>

        <x-adminlte-select name="kategori" label="Kategori">
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-wallet"></i>
                </div>
            </x-slot>
            <x-adminlte-options :options="$kategori->pluck('nama', 'kd_kategori')->toArray()"
                empty-option="Pilih kategori..." />
        </x-adminlte-select>

        <x-adminlte-textarea name="keterangan" label="Keterangan" rows=5 igroup-size="sm"
            placeholder="Tuliskan isi keterangan di sini..." required>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lg fa-file-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="modalKeluarForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-default bg-blue" data-toggle="modal" data-target="#modalMasuk">
        Tambahkan Pemasukan <i class="fas fa-arrow-up"></i>
    </button>
    <button type="button" class="btn btn-default bg-blue" data-toggle="modal" data-target="#modalKeluar">
        <i class="fas fa-arrow-down"></i> Tambahkan Pengeluaran
    </button>
</div>


<table id="KeuanganTable" class="table table-bordered table-striped">
    <thead>
        <tr class="table-primary">
            <th width="5%">No</th>
            <th>Jenis</th>
            <th>Masuk</th>
            <th>Keluar</th>
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
                <td>{{ number_format($keuangan->masuk) }}</td>
                <td>{{ number_format($keuangan->keluar) }}</td>
                <td>{{ $keuangan->kotak->nama }}</td>
                <td>{{ $keuangan->kategori->nama }}</td>
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

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Total Keuangan</h3>
    </div>
    <div class="card-body">
        <div>Total Pemasukan: {{ number_format($keuangans->sum('masuk')) }}</div>
        <div>Total Pengeluaran: {{ number_format($keuangans->sum('keluar')) }}</div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#KeuanganTable').DataTable({
            scrollX: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '200px'
        });
    });
    $('#KeuanganTable').on('click', '.tombol-hapus', function (e) {
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
</script>
@endsection