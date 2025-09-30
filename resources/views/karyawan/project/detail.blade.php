@extends('adminlte::page')

@section('title', 'Detail Project')

@section('content_header')
<h1>Detail Project</h1>
@stop

@section('css')
    <style>
        #modalImage {
            width: auto;
            max-height: 90vh;
            max-width: 100%;
        }

        #imageModal .modal-content {
            width: fit-content;
            height: fit-content;
            border: none;
            padding: 0;
            position: absolute;
        }

        #imageModal .modal-body {
            padding: 0;
        }

        .project-image {
            border-radius: .5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .project-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Project</h3>
                <div class="card-tools float-end">
                    <button type="button" class="btn btn-success tombol-edit" data-toggle="modal"
                        data-target="#editModal"><i class="fas fa-edit"></i> Edit</button>
                    <a href="{{ route('project.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                </div>
            </div>
            <div class="card-body row">
                <div class="col-md-4">
                    @if ($project->foto)
                        <a class="image-popup" data-toggle="modal" data-target="#imageModal"
                            data-src="{{ asset('storage/images/project/' . $project->foto) }}">
                            <img class="img-fluid project-image"
                                src="{{ asset('storage/images/project/' . $project->foto) }}" alt="Foto Project">
                        </a>
                    @else
                        <div class="text-center">
                            <i class="fas fa-camera-retro fa-5x text-muted mb-3"></i>
                            <p class="text-center text-muted m-1">Tidak ada foto.</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <p><strong>Nama Project:</strong> {{ $project->nama_project }}</p>
                    @if ($project->pelanggan)
                        <p><strong>Pelanggan:</strong> {{ $project->pelanggan->nama_pelanggan }}</p>
                    @endif
                    <p><strong>Lokasi:</strong> {{ $project->lokasi }}</p>
                    <p><strong>Tanggal Mulai:</strong> {{ $project->tanggal_mulai }}</p>
                    <p><strong>Deskripsi:</strong> {{ $project->deskripsi }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<x-adminlte-modal id="editModal" title="Edit Project" theme="success" icon="fas fa-edit" size='md'>
    <form id="editForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <x-adminlte-input value="{{ $project->nama_project }}" name="nama_project" label="Nama Project"
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
                        <img id="current_foto_preview" src="{{ asset('storage/images/project/' . $project->foto) }}" alt="Foto Saat Ini"
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
                    <x-adminlte-input-date  name="tanggal_mulai" value="{{ $project->tanggal_mulai ?? date('Y-m-d') }}"
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
                    <x-adminlte-input value="{{ $project->lokasi }}" name="lokasi" label="Lokasi" placeholder="Masukkan Lokasi"
                        required />
                </div>
            </div>
            <div class="col-md-12">
                <x-adminlte-textarea id="edit_deskripsi" name="deskripsi" label="Deskripsi"
                    placeholder="Tuliskan deskripsi project disini...">{{ $project->deskripsi }}</x-adminlte-textarea>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="editForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-modal>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document"
        style="display: flex; align-items: center; justify-content: center;">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });
    </script>
@endsection