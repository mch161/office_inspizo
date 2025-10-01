@extends('adminlte::page')

@section('title', 'Pesanan')

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> Pesanan</h1>
@stop

@section('content')
<div class="card card-primary card-outline">

    <div class="card-header">
        <x-adminlte-button label="Buat Pesanan" theme="primary" data-toggle="modal" data-target="#PesananModal" />
        <div class="card-body">
            <table id="pesananTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Pelanggan</th>
                        <th>Deskripsi Pesanan</th>
                        <th>Tanggal</th>
                        <th width="100px">Status</th>
                        <th width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan as $pesanan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pesanan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pesanan->deskripsi_pesanan, 50) }}</td>
                            <td>{{ $pesanan->tanggal}}</td>
                            <td class="text-center">
                                @if ($pesanan->status == 1)
                                    <span class="badge bg-success">Selesai</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 1)
                                    <span class="badge bg-info">Pesanan Dibuat</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 2)
                                    <span class="badge bg-warning">Pesanan Diterima</span>
                                @elseif ($pesanan->status == 0 && $pesanan->progres == 3)
                                    <span class="badge bg-secondary">Pesanan Diproses</span>
                                @elseif ($pesanan->status == 2)
                                    <span class="badge bg-danger">Pesanan Dibatalkan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pesanan.detail', $pesanan->kd_pesanan)}}"
                                    class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>
                                <button data-toggle="modal" data-target="#EditModal" data-id="{{ $pesanan->kd_pesanan }}"
                                    data-kd_pelanggan="{{ $pesanan->kd_pelanggan }}" data-tanggal="{{ $pesanan->tanggal }}"
                                    data-deskripsi_pesanan="{{ $pesanan->deskripsi_pesanan }}" data-url="{{ route('pesanan.update', $pesanan->kd_pesanan) }}"
                                    class="btn btn-sm btn-primary edit-btn"><i class="fas fa-edit"></i></button>
                                @if ($pesanan->status == 0)
                                    <form action="{{ route('pesanan.update', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="btn btn-danger btn-sm batalkan-btn"><i
                                                class="fas fa-times"></i></button>
                                    </form>
                                @endif
                                @if ($pesanan->status == '2')
                                    <form action="{{ route('pesanan.destroy', $pesanan->kd_pesanan) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm hapus-btn"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                                @if ($pesanan->status == 0 && $pesanan->progres == 2)
                                    <button class="btn btn-sm btn-warning agenda-btn" data-toggle="modal"
                                        data-target="#agendaModal" data-id="{{ $pesanan->kd_pesanan }}"
                                        data-tanggal="{{ $pesanan->tanggal }}"><i class="fas fa-calendar"></i></button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td><span>Tidak ada data</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-adminlte-modal id="PesananModal" title="Buat Pesanan" theme="primary">
        <form id="pesananForm" method="POST" action="{{ route('pesanan.store') }}">
            @csrf
            <x-adminlte-select2 name="kd_pelanggan" label="Pelanggan">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-lg fa-user"></i>
                    </div>
                </x-slot>
                <x-adminlte-options :options="array_column($pelanggan, 'nama_pelanggan', 'kd_pelanggan')"
                    empty-option="Pilih Pelanggan..." />
            </x-adminlte-select2>
            @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" id="tanggal-agenda" :config="$configDate"
                placeholder="Pilih tanggal..." label="Tanggal Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            <label>Deskripsi Pesanan</label>
            <textarea name="deskripsi_pesanan" id="deskripsi_pesanan" class="form-control"></textarea>

            <x-slot name="footerSlot">
                <button type="submit" class="btn btn-primary" id="saveBtn" form="pesananForm">Buat Pesanan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            </x-slot>
        </form>
    </x-adminlte-modal>

    <x-adminlte-modal id="EditModal" title="Edit Pesanan" theme="primary">
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            <x-adminlte-select2 name="kd_pelanggan" label="Pelanggan" id="kd_pelanggan-edit">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-lg fa-user"></i>
                    </div>
                </x-slot>
                <x-adminlte-options :options="array_column($pelanggan, 'nama_pelanggan', 'kd_pelanggan')"
                    empty-option="Pilih Pelanggan..." />
            </x-adminlte-select2>
            @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" id="tanggal-edit" :config="$configDate" placeholder="Pilih tanggal..."
                label="Tanggal Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            <label>Deskripsi Pesanan</label>
            <textarea name="deskripsi_pesanan" id="deskripsi-edit" class="form-control"></textarea>

            <x-slot name="footerSlot">
                <button type="submit" class="btn btn-primary" id="saveBtn" form="editForm">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            </x-slot>
        </form>
    </x-adminlte-modal>

    <x-adminlte-modal id="agendaModal" title="Agendakan Pesanan" theme="primary">
        <form id="agendaForm" method="POST" action="{{ route('pesanan.agenda') }}">
            @csrf
            <input type="hidden" name="kd_pesanan" id="kd_pesanan">
            <label for="title">Nama</label>
            <input class="form-control" type="text" name="title" id="title" placeholder="Nama Agenda" required>

            @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" id="tanggal-agenda2" :config="$configDate"
                placeholder="Pilih tanggal..." label="Tanggal Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            @php $configTime = ['format' => 'HH:mm']; @endphp
            <x-adminlte-input-date name="jam" id="jam-agenda" :config="$configTime" placeholder="Pilih jam..."
                label="Jam Janji Temu" igroup-size="md" required>
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                </x-slot>
            </x-adminlte-input-date>

            <x-slot name="footerSlot">
                <button type="submit" class="btn btn-primary" id="saveBtn" form="agendaForm">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            </x-slot>
        </form>
    </x-adminlte-modal>
</div>
</div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $('#pesananTable').DataTable({
            scrollX: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '50vh',
            searching: false,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)"
            }
        });
        $('.edit-btn').on('click', function (e) {
            e.preventDefault();
            let kd_pesanan = $(this).data('kd_pesanan');
            let deskripsi_pesanan = $(this).data('deskripsi_pesanan');
            let tanggal = $(this).data('tanggal');
            let kd_pelanggan = $(this).data('kd_pelanggan');
            let updateUrl = $(this).data('url');

            $('#kd_pesanan-edit').val(kd_pesanan);
            $('#deskripsi-edit').val(deskripsi_pesanan);
            $('#tanggal-edit').val(tanggal);

            $('#kd_pelanggan-edit').val(kd_pelanggan);
            $('#kd_pelanggan-edit').trigger('change');

            $('#editForm').attr('action', updateUrl);
        });
        $('#pesananTable').on('click', '.batalkan-btn, .hapus-btn', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let title = $(this).hasClass('batalkan-btn') ? 'Batalkan Pesanan' : 'Hapus Pesanan';
            let text = $(this).hasClass('hapus-btn') ? 'Data yang dihapus tidak dapat dikembalikan!' : 'Data yang di ganti tidak dapat dikembalikan!';
            let icon = $(this).hasClass('hapus-btn') ? 'warning' : 'question';
            let confirmButtonText = $(this).hasClass('batalkan-btn') ? 'Ya, Batalkan!' : 'Ya, Hapus!';
            let cancelButtonText = 'Batal';
            let confirmButtonColor = $(this).hasClass('batalkan-btn') ? '#3085d6' : '#d33';
            let cancelButtonColor = $(this).hasClass('hapus-btn') ? '#28a745' : '#d33';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: cancelButtonColor,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
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
                });
                Toast.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                })
            @endif

        $('.agenda-btn').on('click', function () {
            var tanggal = $(this).data('tanggal');
            var kd_pesanan = $(this).data('id');
            $('#tanggal-agenda').val(tanggal);
            $('#kd_pesanan').val(kd_pesanan);
        })
    });
</script>
@stop