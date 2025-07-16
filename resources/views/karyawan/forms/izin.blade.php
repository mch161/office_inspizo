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
        <form action="{{ route('izin.store') }}" method="POST" id="izinForm">
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

            <x-adminlte-input-file name="foto" label="Foto surak dokter (Opsional)" placeholder="Pilih file" show-file-name
                onchange="if(this.files.length){document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';}else{document.getElementById('preview').style.display = 'none';}"  />

            <x-adminlte-textarea name="isi_izin" id="summernote_add" label="Keterangan"></x-adminlte-textarea>

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
        $('#izinTable').DataTable({
            scrollX: true,
            scrollCollapse: true,
            pageLength: 10,
            lengthChange: false,
        });
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
        $('#summernote_edit').summernote(summernoteOptions);
    });
    $(document).ready(function () {
        $('.tombol-edit').on('click', function () {
            const id = $(this).data('id');
            const tanggal = $(this).data('tanggal');
            const jam = $(this).data('jam');
            const isi_izin = $(this).data('isi_izin');

            if (!"{{ $errors->any() && session('invalid_izin') }}") {

                $('#tanggal_edit').val(tanggal);
                $('#jam_edit').val(jam);
                $('#summernote_edit').summernote('code', isi_izin);
            }

            let form = $('#form-edit-izin');
            let updateUrl = "{{ url('izin') }}/" + id;
            form.attr('action', updateUrl);
        });

        @if ($errors->any() && session('invalid_izin'))
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
                text: '{{ session('invalid_izin') }}',
            })
            const errorizinId = "{{ session('error_izin_id') }}";

            if (errorizinId) {
                let form = $('#form-edit-izin');
                let updateUrl = "{{ url('izin') }}/" + errorizinId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @endif
    });
    $('#izinTable').on('click', '.tombol-hapus', function (e) {
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