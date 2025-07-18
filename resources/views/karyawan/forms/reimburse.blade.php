@extends('adminlte::page')

@section('title', 'reimburse')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Form Reimburse</h1>
@stop

@section('css')
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection

@section('content')
<div class="card card-primary card-outline">
    <div class="card-body">
        <form action="{{ route('reimburse.store') }}" method="POST" id="reimburseForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                @php $config = ['format' => 'YYYY-MM-DD']; @endphp
                <x-adminlte-input-date name="tanggal" value="{{ date('Y-m-d') }}" :config="$config"
                    placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" clas required>
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gray mr-3"><i class="fas fa-calendar-day"></i></div>
                    </x-slot>
                </x-adminlte-input-date>

                @php $config = ['format' => 'HH:mm']; @endphp
                <x-adminlte-input-date name="jam" id="jam" :config="$config" placeholder="Pilih jam..." label="Jam"
                    igroup-size="md" required>
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gray"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>


            <div class="mb-3">
                <img id="preview" src="" alt="Image preview"
                    style="max-width: 20%; display: block; padding: 5px;display:none;">
            </div>

            <x-adminlte-input-file name="foto" label="Nota (Kosongkan jika tidak ada)" placeholder="Pilih file..."
                show-file-name
                onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('preview').style.display = 'block';" />

            <x-adminlte-input name="nominal" label="Nominal" placeholder="10000" min='0' type="number">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-gray">
                        <i class="fas"></i>Rp
                    </div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-textarea name="keterangan" label="Keterangan"
                placeholder="Tuliskan keterangan reimburse Anda di sini..."></x-adminlte-textarea>

            <div class="mt-4">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Kirim</button>
                <a href="{{ route('reimburse.form') }}" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#reimburseTable').DataTable({
            scrollX: true,
            scrollCollapse: true,
            pageLength: 10,
            lengthChange: false,
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
    });
    $(document).ready(function () {
        $('.tombol-edit').on('click', function () {
            const id = $(this).data('id');
            const tanggal = $(this).data('tanggal');
            const jam = $(this).data('jam');
            const isi_reimburse = $(this).data('isi_reimburse');

            if (!"{{ $errors->any() && session('invalid_reimburse') }}") {

                $('#tanggal_edit').val(tanggal);
                $('#jam_edit').val(jam);
                $('#summernote_edit').summernote('code', isi_reimburse);
            }

            let form = $('#form-edit-reimburse');
            let updateUrl = "{{ url('reimburse') }}/" + id;
            form.attr('action', updateUrl);
        });

        @if ($errors->any() && session('invalid_reimburse'))
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
                text: '{{ session('invalid_reimburse') }}',
            })
            const errorreimburseId = "{{ session('error_reimburse_id') }}";

            if (errorreimburseId) {
                let form = $('#form-edit-reimburse');
                let updateUrl = "{{ url('reimburse') }}/" + errorreimburseId;
                form.attr('action', updateUrl);
            }

            $('#modalEdit').modal('show');
        @endif
    });
    $('#reimburseTable').on('click', '.tombol-hapus', function (e) {
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