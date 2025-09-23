@extends('adminlte::page')

@section('title', 'Buat Surat Perintah Kerja')

@section('plugins.Select2', true)

@section('content_header')
<h1>Buat Surat Perintah Kerja</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="float-right">
            <a href="{{ route('surat-perintah.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <form action="{{ route('surat-perintah.store') }}" method="POST">
            @csrf
            <div class="mt-3">
                @php
                    $pesanan_config = [
                        'placeholder' => 'Cari pesanan...',
                        'allowClear' => true,
                    ];
                    $project_config = [
                        'placeholder' => 'Cari project...',
                        'allowClear' => true,
                    ];
                    $karyawan_config = [
                        'placeholder' => 'Cari karyawan...',
                        'allowClear' => true
                    ];
                @endphp
                <x-adminlte-select2 name="kd_pesanan" label="Pesanan" :config="$pesanan_config">
                    <option class="text-muted" value="" selected disabled>Cari pesanan...</option>
                    @foreach ($pesanan as $item)
                        <option value="{{ $item->kd_pesanan }}">{{ $item->deskripsi_pesanan }}</option>
                    @endforeach
                </x-adminlte-select2>
                <x-adminlte-select2 name="kd_project" label="Project" :config="$project_config">
                    <option class="text-muted" value="" selected disabled>Cari project...</option>
                    @foreach ($project as $item)
                        <option value="{{ $item->kd_project }}">{{ $item->nama_project }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <x-adminlte-select2 name="kd_karyawan[]" id="karyawan" label="Karyawan" :config="$karyawan_config" multiple>
                @foreach ($karyawan as $item)
                    <option value="{{ $item->kd_karyawan }}">{{ $item->nama }}</option>
                @endforeach
            </x-adminlte-select2>
            @php
                $config = ['format' => 'DD-MM-YYYY'];
            @endphp
            <x-adminlte-input-date name="tanggal_mulai" value="{{ old('tanggal', date('d-m-Y')) }}" :config="$config"
                placeholder="Pilih tanggal..." label="Tanggal Mulai" igroup-size="md">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gray"><i class="fas fa-calendar-day"></i></div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-textarea name="keterangan" label="Keterangan"
                placeholder="Keterangan..."></x-adminlte-textarea>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    </script>
@endsection