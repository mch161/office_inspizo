@extends('adminlte::page')

@section('title', 'Form Pengajuan Izin')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)

@section('content_header')
<h1>Form Pengajuan Izin</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="float-right">
            <a href="{{ route('izin.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <form action="{{ route('izin.store') }}" method="POST" id="izinForm" enctype="multipart/form-data">
            @csrf
            <div class="mt-3 mb-3">
                <x-adminlte-select label="Jenis Izin" name="jenis" id="jenis" class="form-control">
                    <option value="" {{ old('jenis') == null ? 'selected' : '' }} disabled>Pilih jenis izin...</option>
                    <option value="Izin Sakit" {{ old('jenis') == 'Izin Sakit' ? 'selected' : '' }}>Izin Sakit</option>
                    <option value="Izin Keperluan Pribadi" {{ old('jenis') == 'Izin Keperluan Pribadi' ? 'selected' : '' }}>Izin Keperluan Pribadi</option>
                    <option value="Cuti" {{ old('jenis') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Izin Terlambat" {{ old('jenis') == 'Izin Terlambat' ? 'selected' : '' }}>Izin Terlambat
                    </option>
                    <option value="Izin Keluar Kantor" {{ old('jenis') == 'Izin Keluar Kantor' ? 'selected' : '' }}>Izin
                        Keluar Kantor</option>
                </x-adminlte-select>
            </div>
            @php
                $config = ['format' => 'DD-MM-YYYY'];
            @endphp
            <x-adminlte-input-date name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal Izin" igroup-size="md">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gray"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>
            <div id="timeInput">
                @php
                    $config = ['format' => 'HH:mm'];
                @endphp
                <x-adminlte-input-date name="jam" id="jam" value="{{ old('jam', date('H:i')) }}" :config="$config"
                    placeholder="Pilih jam..." label="Dari Jam" igroup-size="md">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gray"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>
            <div id="timeInput2">
                @php
                    $config = ['format' => 'HH:mm'];
                @endphp
                <x-adminlte-input-date name="jam2" id="jam2" :config="$config" placeholder="Pilih jam..."
                    label="Sampai Jam" igroup-size="md">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gray"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>
            <div class="mb-3">
                <img id="preview" src="" alt="Image preview"
                    style="max-width: 200px; max-height: 200px; display: none; border-radius: .5rem; margin-top: 10px;">
            </div>

            <label for="foto">Foto <sup class="text-muted">(Opsional)</sup></label>
            <x-adminlte-input-file name="foto" placeholder="Pilih file..." igroup-size="md"
                onchange="previewImage(event)" />

            <x-adminlte-textarea name="keterangan" label="Keterangan"
                placeholder="Tuliskan keterangan izin Anda di sini..."></x-adminlte-textarea>

            <div class="mt-4">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Kirim</button>
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
            $('#timeInput').hide();
            $('#timeInput2').hide();
            $('#jenis').val() === 'Izin Terlambat' ? $('#timeInput2').show() : $('#timeInput2').hide();
            $('#jenis').val() === 'Izin Keluar Kantor' ? $('#timeInput').show() : $('#timeInput').hide();
            $('#jenis').val() === 'Izin Keluar Kantor' ? $('#timeInput2').show() : $('#timeInput2').hide();
            $('#jenis').change(function () {
                $('#timeInput').hide();
                if ($(this).val() === 'Izin Terlambat') {
                    $('#timeInput2').show();
                }
                if ($(this).val() === 'Izin Keluar Kantor') {
                    $('#timeInput').show();
                    $('#timeInput2').show();
                }
            })
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