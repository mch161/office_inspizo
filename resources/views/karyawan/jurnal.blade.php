@extends('adminlte::page')

@section('title', 'Jurnal')

@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>Jurnal</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('content')

<x-adminlte-modal id="modalTambah" title="Tambahkan Jurnal" theme="success" icon="fas fa-clipboard" size='lg'>
    <form action="{{ route('jurnal.store') }}" method="POST" id="jurnalForm">
        @csrf
        <div class="row">
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

            {{-- Time picker --}}
            @php
                $config = ['format' => 'HH:mm'];
            @endphp
            <x-adminlte-input-date name="jam" :config="$config" placeholder="Pilih jam..." label="Jam" igroup-size="md"
                required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-clock"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
        </div>

        <x-adminlte-textarea name="isi_jurnal" label="Isi Jurnal" rows=5 igroup-size="sm"
            placeholder="Tuliskan isi jurnal di sini..." required>
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-lg fa-file-alt text-warning"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="jurnalForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="modalEdit" title="Edit Jurnal" theme="primary" icon="fas fa-edit" size='lg'>
    <form method="POST" id="form-edit-jurnal">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Date picker --}}
            @php
                $config = ['format' => 'YYYY-MM-DD'];
            @endphp
            <x-adminlte-input-date name="tanggal_edit" id="tanggal_edit" :config="$config" placeholder="Pilih tanggal..." label="Tanggal"
                igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>

            {{-- Time picker --}}
            @php
                $config = ['format' => 'HH:mm'];
            @endphp
            <x-adminlte-input-date name="jam_edit" id="jam_edit" :config="$config" placeholder="Pilih jam..." label="Jam" igroup-size="md"
                required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fas fa-clock"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
        </div>

        <x-adminlte-textarea name="isi_jurnal_edit" id="isi_jurnal_edit" label="Isi Jurnal" rows=5 igroup-size="sm"
            placeholder="Tuliskan isi jurnal di sini..." required>
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-lg fa-file-alt text-warning"></i>
                </div>
            </x-slot>
        </x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Edit" type="submit" form="form-edit-jurnal"/>
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-button label="Tambahkan Jurnal" class="float-right mb-2 bg-blue" data-toggle="modal"
    data-target="#modalTambah" />

<table id="JurnalTable" class="display">
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
                <td>{{ $jurnal->dibuat_oleh }}</td>
                <td>{{ $jurnal->isi_jurnal }}</td>
                <td>
                    <button class="btn btn-primary btn-sm tombol-edit" data-toggle="modal" data-target="#modalEdit"
                        data-id="{{ $jurnal->kd_jurnal }}" data-tanggal="{{ $jurnal->tanggal }}"
                        data-jam="{{ $jurnal->jam }}" data-isi_jurnal="{{ $jurnal->isi_jurnal }}">
                        Edit
                    </button>

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
        $('#JurnalTable').DataTable();
    });

    $('.tombol-edit').on('click', function () {
        let id = $(this).data('id');
        let tanggal = $(this).data('tanggal');
        let jam = $(this).data('jam');
        let isi_jurnal = $(this).data('isi_jurnal');

        $('#tanggal_edit').val(tanggal);
        $('#jam_edit').val(jam);
        $('#isi_jurnal_edit').val(isi_jurnal);

        let form = $('#form-edit-jurnal');
        let updateUrl = "{{ route('jurnal.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', id);
        form.attr('action', updateUrl);
    });

    $('#JurnalTable').on('click', '.tombol-hapus', function (e) {
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
</script>
@endsection