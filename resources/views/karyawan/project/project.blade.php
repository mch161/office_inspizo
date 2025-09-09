@extends('adminlte::page')

@section('title', 'Project')

@section('content_header')
<h1>Project</h1>
@stop

@section('content')
<x-adminlte-modal id="tambahModal" title="Tambah Project" theme="success" icon="fas fa-plus-circle" size='md'>
    <form id="projectForm" action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <x-adminlte-input name="nama_project" label="Nama Project" placeholder="Masukkan Nama Project"
                        required />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <img id="preview" src="" alt="Image preview"
                        style="max-width: 20%; display: block; padding: 5px;display:none;">
                </div>
                <div class="form-group">
                    <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name
                        onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    @php $config = ['format' => 'YYYY-MM-DD']; @endphp
                    <x-adminlte-input-date name="tanggal_mulai" value="{{ date('Y-m-d') }}" :config="$config"
                        placeholder="Pilih tanggal..." label="Tanggal Mulai" igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-adminlte-input name="lokasi" label="Lokasi" placeholder="Masukkan Lokasi" required />
                </div>
            </div>
            <div class="col-md-12">
                <x-adminlte-textarea name="deskripsi" label="Deskripsi"
                    placeholder="Tuliskan deskripsi project disini..."></x-adminlte-textarea>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="projectForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="editModal" title="Edit Project" theme="success" icon="fas fa-edit" size='md'>
    <form id="editForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <x-adminlte-input id="edit_nama_project" name="nama_project" label="Nama Project"
                        placeholder="Masukkan Nama Project" required />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label>Foto Saat Ini</label>
                        <label style="margin-left: auto;display:none;" id="label_foto_baru">Foto Baru</label>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <img id="current_foto_preview" src="" alt="Foto Saat Ini"
                            style="max-width: 100px; max-height: 100px; margin-bottom: 10px;">
                        <img id="preview_edit" src="" alt="Image preview"
                            style="max-width: 20%; display: block; padding: 5px;display:none;">
                    </div>
                    <div class="mb-3">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <x-adminlte-input-file id="edit_foto" name="foto"
                        label="Upload Foto Baru (Kosongkan jika tidak ingin ganti)" placeholder="Pilih file"
                        onchange="document.getElementById('preview_edit').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview_edit').style.display = 'block';document.getElementById('label_foto_baru').style.display = 'block';" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    @php $config = ['format' => 'YYYY-MM-DD']; @endphp
                    <x-adminlte-input-date id="edit_tanggal_mulai" name="tanggal_mulai" value="{{ date('Y-m-d') }}"
                        :config="$config" placeholder="Pilih tanggal..." label="Tanggal Mulai" igroup-size="md"
                        required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-adminlte-input id="edit_lokasi" name="lokasi" label="Lokasi" placeholder="Masukkan Lokasi"
                        required />
                </div>
            </div>
            <div class="col-md-12">
                <x-adminlte-textarea id="edit_deskripsi" name="deskripsi" label="Deskripsi"
                    placeholder="Tuliskan deskripsi project disini..."></x-adminlte-textarea>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="editForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<div class="d-flex justify-content-end mb-4">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahModal">Tambah
        Project</button>
</div>
<form action="{{ route('project.search') }}" method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="s" class="form-control" placeholder="Cari nama project..." value="{{ request('s') }}">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </div>
</form>

<div class="row">
    @foreach ($projects as $project)
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $project->nama_project }}</h3>
                    <span
                        class="badge {{ $project->status == 'Belum Selesai' ? 'badge-warning' : 'badge-success' }} float-right">{{ $project->status }}</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('storage/images/project/' . $project->foto) }}" alt="Foto Project"
                        class="card-img-top">
                    <p>{{ Str::limit($project->deskripsi, 100) }}
                        @if (strlen($project->deskripsi) > 100)
                            <a class="read-more">Baca Selengkapnya</a>
                        @endif
                    </p>
                    <div class="deskripsi-full" style="display: none;">
                        {{ $project->deskripsi }}
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success tombol-edit" data-toggle="modal" data-target="#editModal"
                        data-kd_project="{{ $project->kd_project }}" data-nama_project="{{ $project->nama_project }}"
                        data-foto="{{ asset('storage/images/project/' . $project->foto) }}"
                        data-tanggal_mulai="{{ $project->tanggal_mulai }}" data-lokasi="{{ $project->lokasi }}"
                        data-deskripsi="{{ $project->deskripsi }}">Edit</button>
                    <a href="{{ route('project.detail', $project->kd_project) }}" class="btn btn-primary">Detail</a>
                    <form action="{{ route('project.destroy', $project->kd_project) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger tombol-hapus">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid" alt="Barang Image">
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        $('.read-more').click(function () {
            $(this).parent('p').hide();
            $(this).parent('p').next('.deskripsi-full').show();
            $(this).hide();
        });
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });
        $(document).ready(function () {
            $('.tombol-edit').on('click', function () {
                const id = $(this).data('kd_project');
                const nama_project = $(this).data('nama_project');
                const fotoUrl = $(this).data('foto');
                const tanggal_mulai = $(this).data('tanggal_mulai');
                const lokasi = $(this).data('lokasi');
                const deskripsi = $(this).data('deskripsi');

                $('#edit_nama_project').val(nama_project);
                $('#current_foto_preview').attr('src', fotoUrl);
                $('#edit_tanggal_mulai').val(tanggal_mulai);
                $('#edit_lokasi').val(lokasi);
                $('#edit_deskripsi').val(deskripsi);

                let form = $('#editForm');
                let updateUrl = "{{ url('project') }}/" + id;
                form.attr('action', updateUrl);
            });
        })
        $(document).on('click', '.tombol-hapus', function (e) {
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
        })
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