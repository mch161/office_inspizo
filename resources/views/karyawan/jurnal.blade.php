@extends('adminlte::page')

@section('title', 'Jurnal')

@section('plugins.Sweetalert2', true)

@section('content_header')
<div class="flex justify-content-between">
    <h1>Jurnal</h1>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('content')

<x-adminlte-modal id="modalPurple" title="Tambahkan Jurnal" theme="purple" icon="fas fa-clipboard" size='lg'>
    <form action="{{ route('jurnal.store') }}" method="POST">
        @csrf
        {{-- SM size, restricted to current month and week days --}}
        @php
            $config = [
                'format' => 'YYYY-MM-DD HH.mm',
                'dayViewHeaderFormat' => 'MMM YYYY',
                'minDate' => "js:moment().startOf('month')",
                'maxDate' => "js:moment().endOf('month')",
                'daysOfWeekDisabled' => [0, 6],
            ];
        @endphp
        <x-adminlte-input-date name="idSizeSm" label="Working Datetime" igroup-size="sm" :config="$config"
            placeholder="Choose a working day...">
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-input-date>
        <x-adminlte-textarea name="isi_jurnal" label="Isi Jurnal" rows=5 igroup-size="sm"
            placeholder="Tuliskan isi jurnal di sini...">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-lg fa-file-alt text-warning"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-button label="Tambahkan Jurnal" class="float-right mb-2 bg-blue" data-toggle="modal"
    data-target="#modalPurple" />

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Nama Karyawan</th>
            <th>Isi Jurnal</th>
            <th width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jurnals as $jurnal)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $jurnal->tanggal }}</td>
                <td>{{ $jurnal->jam }}</td>
                <td>{{ $jurnal->karyawan->nama ?? 'N/A' }}</td>
                <td>{{ $jurnal->isi_jurnal }}</td>
                <td>
                    <a href="#" class="edit btn btn-primary btn-sm">Edit</a>

                    <form action="{{ route('jurnal.destroy', $jurnal->kd_jurnal) }}" method="POST" style="display:inline;">
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
    $(document).ready(function () {
        $('#myTable').DataTable();
    });

    $('#myTable').on('click', '.tombol-hapus', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Yakin ingin menghapus?',
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
        })
    });
</script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
@endsection