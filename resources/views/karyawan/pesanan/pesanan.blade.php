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
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Deskripsi Pesanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                                    <span class="badge bg-danger">Batal</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pesanan.detail', $pesanan->kd_pesanan)}}"
                                    class="btn btn-sm btn-info view-btn"><i class="fas fa-eye"></i></a>
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
            <x-adminlte-select name="kd_pelanggan" label="Pelanggan">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-lg fa-user"></i>
                    </div>
                </x-slot>
                <x-adminlte-options :options="array_column($pelanggan, 'nama_pelanggan', 'kd_pelanggan')"
                    empty-option="Pilih Pelanggan..." />
            </x-adminlte-select>
            @php $config = ['format' => 'd/m/Y']; @endphp
            <x-adminlte-input-date name="tanggal" value="{{ date('d/m/Y') }}" :config="$config"
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

    <x-adminlte-modal id="agendaModal" title="Agendakan Pesanan" theme="primary">
        <form id="agendaForm" method="POST" action="{{ route('pesanan.agenda') }}">
            @csrf
            <input type="hidden" name="kd_pesanan" id="kd_pesanan">
            <input class="form-control" type="text" name="title" id="title" placeholder="Nama Agenda" required>
            
            @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
            <x-adminlte-input-date name="tanggal" id="tanggal-agenda" :config="$configDate"
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