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
                    <a href="{{ route('project.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i>
                        Kembali</a>
                </div>
            </div>
            <div class="card-body row">
                <div class="col-md-4">
                    @if ($project->foto)
                        <a class="image-popup" data-toggle="modal" data-target="#imageModal"
                            data-src="{{ asset('storage/images/project/' . $project->foto) }}">
                            <img class="img-fluid project-image" src="{{ asset('storage/images/project/' . $project->foto) }}"
                                alt="Foto Project">
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