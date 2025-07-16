@extends('adminlte::page')

@section('title', 'Form Pengajuan Izin')

{{-- Include SweetAlert2 and Summernote plugins --}}
@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
    <h1>Form Pengajuan Izin</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Formulir Pengajuan Izin</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('izin.store') }}" method="POST" id="izinForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        @php
                            $config = ['format' => 'YYYY-MM-DD'];
                        @endphp
                        <x-adminlte-input-date name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" :config="$config"
                            placeholder="Pilih tanggal..." label="Tanggal Izin" igroup-size="md" required>
                            <x-slot name="appendSlot">
                                <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                    <div class="col-md-6">
                         @php
                            $config = ['format' => 'HH:mm'];
                        @endphp
                        <x-adminlte-input-date name="jam" id="jam" value="{{ old('jam', date('H:i')) }}" :config="$config" placeholder="Pilih jam..." label="Jam"
                            igroup-size="md" required>
                            <x-slot name="appendSlot">
                                <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                </div>

                <div class="mb-3">
                    <img id="preview" src="" alt="Image preview"
                        style="max-width: 200px; max-height: 200px; display: none; border-radius: .5rem; margin-top: 10px;">
                </div>

                <x-adminlte-input-file name="foto" label="Foto Surat Dokter (Opsional)" placeholder="Pilih file..."
                    igroup-size="md" onchange="previewImage(event)" />

                @php
                $config = [
                    "height" => "150",
                    "toolbar" => [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ],
                ];
                @endphp
                <x-adminlte-text-editor name="keterangan" label="Keterangan" igroup-size="md"
                    placeholder="Tuliskan alasan izin Anda di sini..." :config="$config">{{ old('keterangan') }}</x-adminlte-text-editor>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('izin.form') }}" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        if (event.target.files.length > 0) {
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }

    $(document).ready(function () {
        // Display success/error toasts if they exist
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
    });
</script>
@endsection
