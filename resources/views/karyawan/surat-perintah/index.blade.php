@extends('adminlte::page')

@section('title', 'Surat Perintah Kerja')

@section('content_header')
<h1>Surat Perintah Kerja</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('superadmin')
                    <a href="{{ route('surat-perintah.create') }}" class="btn btn-sm btn-primary mb-3 float-right"><i
                            class="fas fa-plus"></i> Buat Surat Perintah Kerja</a>
                @endcan
                <table id="suratPerintahTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            @can('superadmin')
                                <th>Karyawan</th>
                            @endcan
                            <th>Pesanan / Project</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surat_perintah as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                @can('superadmin')
                                    <td>{{ $item->karyawan->nama }}</td>
                                @endcan
                                <td>
                                    @if ($item->pesanan)
                                        <a href="{{ route('pesanan.detail', $item->pesanan->kd_pesanan) }}">
                                            {{ $item->pesanan->deskripsi_pesanan ?? '-' }}
                                        </a>
                                    @elseif ($item->project)
                                        <a href="{{ route('project.detail', $item->project->kd_project) }}">
                                            {{ $item->project->nama_project ?? '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    @if ($item->tanggal_selesai && \Carbon\Carbon::parse($item->tanggal_mulai)->isSameDay(\Carbon\Carbon::parse($item->tanggal_selesai)))
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                                    @elseif ($item->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} -
                                        {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == '0')
                                        <div class="badge badge-warning">Menunggu</div>
                                    @else
                                        <div class="badge badge-success">Selesai</div>
                                    @endif
                                </td>
                                <td>
                                    @can('superadmin')
                                        <form action="{{ route('surat-perintah.destroy', $item->kd_surat_perintah_kerja) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger tombol-hapus"><i
                                                    class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    @endcan
                                    @if (Auth::guard('karyawan')->user()->kd_karyawan == $item->kd_karyawan && $item->status == '0')
                                        <button data-toggle="modal" data-target="#selesaiModal"
                                            data-id="{{ $item->kd_surat_perintah_kerja }}"
                                            class="btn btn-sm btn-success tombol-selesai"><i class="fas fa-check"></i>
                                            Tandai Selesai</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@cannot('superadmin')
    <x-adminlte-modal id="selesaiModal" title="Tandai Selesai" theme="success">
        <form id="form-selesai" action="{{ route('surat-perintah.update', $item->kd_surat_perintah_kerja) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="1">
            @php
                $config = ['format' => 'DD-MM-YYYY'];
            @endphp
            <x-adminlte-input-date name="tanggal_selesai" value="{{ old('tanggal', date('d-m-Y')) }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal Selesai" igroup-size="md">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gray"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>
            <x-slot name="footerSlot">
                <button type="submit" form="form-selesai" class="btn btn-success">Tandai Selesai</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            </x-slot>
        </form>
    </x-adminlte-modal>
@endcannot

@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#suratPerintahTable').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                pageLength: 10,
                scrollX: true,
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Cari:",
                    searchPlaceholder: "Cari data..."
                }
            });
            $('.tombol-hapus').on('click', function (e) {
                let form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            })
            $('.tombol-selesai').on('click', function (e) {
                let id = $(this).data('id');
                let form = $('#form-selesai');
                let updateUrl = "{{ url('surat-perintah') }}/" + id;
                form.attr('action', updateUrl);
            })
        });
        @if (session()->has('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
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
    </script>
@endsection