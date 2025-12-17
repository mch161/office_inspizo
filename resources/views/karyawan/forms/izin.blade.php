@extends('adminlte::page')

@section('title', 'Form Pengajuan Izin')

@section('plugins.Sweetalert2', true)
@section('plugins.Summernote', true)
@section('plugins.DateRangePicker', true)

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
                <x-adminlte-select label="Jenis Izin" name="jenis" id="jenis">
                    <option value="" disabled selected>Pilih jenis izin...</option>
                    <option value="Izin Sakit">Izin Sakit</option>
                    <option value="Izin Keperluan Pribadi">Izin Keperluan Pribadi</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Izin Terlambat">Izin Terlambat</option>
                    <option value="Izin Keluar Kantor">Izin Keluar Kantor</option>
                </x-adminlte-select>
            </div>
            @php
                $config = [
                    "locale" => ["format" => "DD-MM-YYYY"],
                    "autoUpdateInput" => false,
                ];
            @endphp
            <x-adminlte-date-range name="tanggal" label="Tanggal / Rentang Izin" :config="$config"
                placeholder="Pilih Tanggal...">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-date-range>

            <div id="timeInput" style="display: none;">
                <x-adminlte-input-date name="jam" id="jam" :config="['format' => 'HH:mm']" label="Dari Jam">
                    <x-slot name="appendSlot">
                        <div class="input-group-text"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>
            <div id="timeInput2" style="display: none;">
                <x-adminlte-input-date name="jam2" id="jam2" :config="['format' => 'HH:mm']" label="Sampai Jam">
                    <x-slot name="appendSlot">
                        <div class="input-group-text"><i class="fas fa-clock"></i></div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="mb-3">
                <img id="preview" src="" alt="Image preview"
                    style="max-width: 200px; max-height: 200px; display: none; border-radius: .5rem; margin-top: 10px;">
            </div>
            <x-adminlte-input-file name="foto" label="Foto (Opsional)" onchange="previewImage(event)"/>
            <x-adminlte-textarea name="keterangan" label="Keterangan"  />

            <button type="submit" class="btn btn-success">Kirim</button>
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
        $('input[name="tanggal"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        });
        $('input[name="tanggal"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        $('#jenis').change(function () {
            $('#timeInput, #timeInput2').hide();
            if ($(this).val() == 'Izin Terlambat') $('#timeInput2').show();
            if ($(this).val() == 'Izin Keluar Kantor') { $('#timeInput').show(); $('#timeInput2').show(); }
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
    });
</script>
@stop