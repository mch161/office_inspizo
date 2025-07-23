@extends('adminlte::page')

@section('title', 'Daftar Pesanan')

@section('content_header')
<h1>Daftar Pesanan</h1>
@stop
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('css')
    <style>
        .progress-bar-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress {
            flex-grow: 1;
            height: 1.25rem;
        }

        .progress-bar-label {
            font-weight: 600;
            width: 40px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: .5rem;
        }
    </style>
@endsection

@section('content')
{{-- This button triggers the "Add" modal --}}

<x-adminlte-button label="Tambahkan Pesanan" class="float-right mb-2 bg-blue" data-toggle="modal"
    data-target="#modalTambah" />

<table id="PesananTable" class="table table-bordered table-striped table-hover">
    <thead class="table-primary">
        <tr>
            <th width="5%">No</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th width="30%">Progres</th>
            <th>Dibuat Oleh</th>
            <th width="15%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pesanan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="white-space: normal; text-align: left;">{!! $item->deskripsi_pesanan !!}</td>
                <td>
                    @php
                        $statusClass = 'badge-secondary';
                        if ($item->status == 'Baru')
                            $statusClass = 'badge-info';
                        if ($item->status == 'Dikerjakan')
                            $statusClass = 'badge-warning';
                        if ($item->status == 'Selesai')
                            $statusClass = 'badge-success';
                        if ($item->status == 'Dibatalkan')
                            $statusClass = 'badge-danger';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $item->status }}</span>
                </td>
                <td>
                    <div class="progress-bar-container">
                        @php
                            $progressClass = 'bg-info';
                            if ($item->progres >= 40)
                                $progressClass = 'bg-warning';
                            if ($item->progres >= 80)
                                $progressClass = 'bg-success';
                        @endphp
                        <div class="progress">
                            <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                style="width: {{ $item->progres }}%;" aria-valuenow="{{ $item->progres }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <span class="progress-bar-label">{{ $item->progres }}%</span>
                    </div>
                </td>
                <td>{{ $item->dibuat_oleh }}</td>
                <td class="action-buttons">
                    {{-- This button triggers the "Edit" modal and passes data --}}
                    <button class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editPesanan  "
                        data-id="{{ $item->id }}" data-deskripsi="{{ $item->deskripsi_pesanan }}"
                        data-status="{{ $item->status }}" data-progres="{{ $item->progres }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('pesanan.destroy', $item) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Add Pesanan Modal --}}
<x-adminlte-modal id="modalTambah" title="Tambahkan pesanan" theme="success" icon="fas fa-clipboard" size='lg'>
    <form action="{{ route('pesanan.store') }}" method="POST" id="modalTambah">
        @csrf
        <div class="row">
            @php $config = ['format' => 'YYYY-MM-DD']; @endphp
            <x-adminlte-input-date name="tanggal" value="{{ date('Y-m-d') }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            @php $config = ['format' => 'HH:mm']; @endphp
            <x-adminlte-input-date name="jam" id="jam" :config="$config" placeholder="Pilih jam..." label="Jam"
                igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                </x-slot>
            </x-adminlte-input-date>
        </div>

        <x-adminlte-textarea name="deskripsi_pesanan" id="summernote_add" label="Keterangan"></x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="pesananForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

{{-- Edit Pesanan Modal --}}
<form id="editPesananForm" method="POST">
    @csrf
    @method('PUT')
    <x-adminlte-modal id="editPesananModal" title="Edit Pesanan" theme="info" icon="fas fa-edit" size='lg'
        disable-animations>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" type="submit" label="Update"></x-adminlte-button>
            <x-adminlte-button theme="danger" label="Batal" data-dismiss="modal"></x-adminlte-button>
        </x-slot>
    </x-adminlte-modal>
</form>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#PesananTable').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
            });

            // Summernote config
            const summernoteConfig = {
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol', 'paragraph']],
                ]
            };
            $('#deskripsi_create').summernote(summernoteConfig);
            $('#deskripsi_edit').summernote(summernoteConfig);

            // Handle Edit Button Click
            $('.edit-btn').on('click', function () {
                const id = $(this).data('id');
                const deskripsi = $(this).data('deskripsi');
                const status = $(this).data('status');
                const progres = $(this).data('progres');

                // Set form action URL
                const updateUrl = "{{ url('pesanan') }}/" + id;
                $('#editPesananForm').attr('action', updateUrl);

                // Populate modal fields
                $('#editPesananModal #deskripsi_edit').summernote('code', deskripsi);
                $('#editPesananModal #status_edit').val(status);
                $('#editPesananModal #progres_edit').val(progres);
            });

            // Handle Delete Button Confirmation
            $('.delete-form').on('submit', function (e) {
                e.preventDefault();
                let form = this;
                Swal.fire({
                    title: 'Anda yakin?',
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

            // Re-open modal if validation fails
            @if($errors->any())
                @if(session('form_type') === 'create')
                    $('#addPesananModal').modal('show');
                @elseif(session('form_type') === 'edit')
                    $('#editPesananModal').modal('show');
                @endif
            @endif

            // Display success toast
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
                });
    </script>
@endsection