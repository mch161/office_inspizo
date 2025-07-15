@extends('adminlte::page')

@section('title', 'Jurnal')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Form Reimburse</h1>
@stop

@section('css')

@endsection

@section('content')
<x-adminlte-card label="" title="Form Reimburse" theme="info" icon="fas fa-lg fa-bell" maximizable>
        <form action="{{ route('jurnal.store') }}" method="POST" id="jurnalForm">
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

        <x-adminlte-input-file name="foto" label="Upload file" placeholder="Pilih file" show-file-name
            onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />

        <x-adminlte-textarea name="isi_jurnal" id="summernote_add" label="Keterangan"></x-adminlte-textarea>

        <x-slot name="footerSlot">
            <x-adminlte-button theme="success" label="Simpan" type="submit" form="jurnalForm" />
            <x-adminlte-button label="Batal" data-dismiss="modal" theme="danger" />
        </x-slot>
    </form>
</x-adminlte-card>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#JurnalTable').DataTable({
            scrollX: true,
            scrollCollapse: true,
            pageLength: 10,
            lengthChange: false,
        });
        var summernoteOptions = {
            height: 250,
            placeholder: 'Masukkan keterangan jurnal di sini...',
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
            const isi_jurnal = $(this).data('isi_jurnal');

            if (!"{{ $errors->any() && session('invalid_jurnal') }}") {

                $('#tanggal_edit').val(tanggal);
                $('#jam_edit').val(jam);
                $('#summernote_edit').summernote('code', isi_jurnal);
            }

            let form = $('#form-edit-jurnal');
            let updateUrl = "{{ url('jurnal') }}/" + id;
            form.attr('action', updateUrl);
        });

        @if ($errors->any() && session('invalid_jurnal'))
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
                text: '{{ session('invalid_jurnal') }}',
            })
            const errorJurnalId = "{{ session('error_jurnal_id') }}";

            if (errorJurnalId) {
                let form = $('#form-edit-jurnal');
                let updateUrl = "{{ url('jurnal') }}/" + errorJurnalId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @endif
    });
    $('#JurnalTable').on('click', '.tombol-hapus', function (e) {
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