@extends('adminlte::page')

@section('title', 'Data Kunjungan')

@section('content_header')
<h1>Tambah Kunjungan</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.Summernote', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('kunjungan.index') }}" class="btn btn-sm btn-primary mb-3 float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
            <form action="{{ route('kunjungan.store') }}" method="POST" id="kunjunganForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="kd_pelanggan">Pelanggan</label>
                    <x-adminlte-select2 name="kd_pelanggan" id="kd_pelanggan" required>
                        <option value="" disabled selected>Pilih Pelanggan...</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->kd_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>

                <div class="form-group">
                    <label for="kd_pesanan">Pesanan</label>
                    <x-adminlte-select2 name="kd_pesanan" id="kd_pesanan" required disabled>
                        <option value="" disabled selected>Pilih Pelanggan terlebih dahulu</option>
                    </x-adminlte-select2>
                </div>
                <div class="form-group">
                    <label for="kd_karyawan">Karyawan</label>
                    @php
                        $karyawan = App\Models\Karyawan::all();
                        $karyawan_config = ['allowClear' => true, 'placeholder' => 'Cari karyawan...'];
                    @endphp
                    <x-adminlte-select2 name="kd_karyawan[]" :config="$karyawan_config" id="kd_karyawan" required multiple>
                        @foreach ($karyawan as $karyawan)
                            <option value="{{ $karyawan->kd_karyawan }}">{{ $karyawan->nama }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal Kunjungan</label>
                    @php $configDate = ['format' => 'DD/MM/YYYY']; @endphp
                    <x-adminlte-input-date name="tanggal" id="tanggal-agenda" :config="$configDate"
                        placeholder="Pilih tanggal..." igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="0">Belum Selesai</option>
                        <option value="1">Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" required></textarea>
                </div>
                <div class="form-group">
                    <x-adminlte-button theme="success" label="Simpan" type="submit" form="kunjunganForm" />
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).on('select2:open', () => {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

        $(document).ready(function () {
            var summernoteOptions = {
                height: 250,
                placeholder: 'Masukkan keterangan di sini...',
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

            $('#keterangan').summernote(summernoteOptions);
        });

        $(document).ready(function () {
            $('#kd_pelanggan').on('change', function () {
                var kdPelanggan = $(this).val();
                var pesananSelect = $('#kd_pesanan');

                pesananSelect.empty().append('<option value="" disabled selected>Memuat...</option>').prop('disabled', true);

                if (kdPelanggan) {
                    $.ajax({
                        url: '{{ url("/get-pesanan-by-pelanggan") }}/' + kdPelanggan,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            pesananSelect.prop('disabled', false);
                            pesananSelect.empty().append('<option value="" disabled selected>Pilih Pesanan...</option>');

                            if (data.length > 0) {
                                $.each(data, function (key, value) {
                                    pesananSelect.append('<option value="' + value.kd_pesanan + '">' + value.deskripsi_pesanan + '</option>');
                                });
                            } else {
                                pesananSelect.empty().append('<option value="" disabled selected>Tidak ada pesanan untuk pelanggan ini</option>');
                            }
                        },
                        error: function () {
                            pesananSelect.empty().append('<option value="" disabled selected>Gagal memuat data</option>');
                        }
                    });
                } else {
                    pesananSelect.empty().append('<option value="" disabled selected>Pilih Pelanggan terlebih dahulu</option>').prop('disabled', true);
                }

                pesananSelect.trigger('change');
            });
        });
    </script>
@endsection