@extends('adminlte::page')

@section('title', 'izin')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Form Izin</h1>
@stop

@section('css')

@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">Formulir</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('izin.store') }}" method="POST" id="izinForm" enctype="multipart/form-data">
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

            <div class="mb-3">
                <img id="preview" src="" alt="Image preview"
                    style="max-width: 20%; display: block; padding: 5px;display:none;">
            </div>

            <x-adminlte-input-file name="foto" label="Foto surat dokter (Opsional)" placeholder="Pilih file" show-file-name
                onchange="if(this.files.length){document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';}else{document.getElementById('preview').style.display = 'none';}"  />

            <x-adminlte-textarea name="keterangan" id="summernote_add" label="Keterangan"></x-adminlte-textarea>

            <div name="card-footer">
                <x-adminlte-button theme="success" label="Simpan" type="submit" form="izinForm" />
                <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        var summernoteOptions = {
            height: 250,
            placeholder: 'Masukkan keterangan izin di sini...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        };

        $('#summernote_add').summernote(summernoteOptions);
    });
    $(document).ready(function () {
        const timeInput = $('#jam');
        function updateRealTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            timeInput.val(`${hours}:${minutes}`);
        }
        setInterval(updateRealTime, 1000);
        updateRealTime();
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