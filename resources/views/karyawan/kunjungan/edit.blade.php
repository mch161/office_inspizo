@extends('adminlte::page')

@section('title', 'Edit Kunjungan')

@section('content_header')
    <h1>Edit Kunjungan</h1>
@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.Summernote', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('kunjungan.index') }}" class="btn btn-sm btn-primary mb-3 float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
            
            <form action="{{ route('kunjungan.update', $kunjungan->kd_kunjungan) }}" method="POST" id="kunjunganForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="kd_pelanggan">Pelanggan</label>
                    <x-adminlte-select2 name="kd_pelanggan" id="kd_pelanggan" required>
                        <option value="" disabled>Pilih Pelanggan...</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->kd_pelanggan }}" 
                                {{ old('kd_pelanggan', $kunjungan->kd_pelanggan) == $pelanggan->kd_pelanggan ? 'selected' : '' }}>
                                {{ $pelanggan->nama_pelanggan }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>

                <div class="form-group">
                    <label for="kd_pesanan">Pesanan</label>
                    <x-adminlte-select2 name="kd_pesanan" id="kd_pesanan" required>
                        <option value="" disabled selected>Pilih Pelanggan terlebih dahulu</option>
                    </x-adminlte-select2>
                </div>

                <div class="form-group">
                    <label for="kd_karyawan">Karyawan</label>
                    @php
                        $allKaryawan = App\Models\Karyawan::orderBy('nama')->get();
                        $selectedKaryawan = $kunjungan->karyawans->pluck('kd_karyawan')->toArray();
                        $karyawan_config = ['allowClear' => true, 'placeholder' => 'Cari karyawan...'];
                    @endphp
                    <x-adminlte-select2 name="kd_karyawan[]" :config="$karyawan_config" id="kd_karyawan" required multiple>
                        @foreach ($allKaryawan as $karyawan)
                            <option value="{{ $karyawan->kd_karyawan }}" 
                                {{ in_array($karyawan->kd_karyawan, old('kd_karyawan', $selectedKaryawan)) ? 'selected' : '' }}>
                                {{ $karyawan->nama }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                
                <div class="form-group">
                    <label for="tanggal">Tanggal Kunjungan</label>
                    @php 
                        $tanggalValue = old('tanggal', \Carbon\Carbon::parse($kunjungan->tanggal)->format('d/m/Y'));
                        $configDate = ['format' => 'DD/MM/YYYY']; 
                    @endphp
                    <x-adminlte-input-date name="tanggal" id="tanggal-agenda" :config="$configDate"
                        placeholder="Pilih tanggal..." igroup-size="md" required value="{{ $tanggalValue }}">
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="0" {{ old('status', $kunjungan->status) == '0' ? 'selected' : '' }}>Belum Selesai</option>
                        <option value="1" {{ old('status', $kunjungan->status) == '1' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" required>{{ old('keterangan', $kunjungan->keterangan) }}</textarea>
                </div>

                <div class="form-group">
                    <x-adminlte-button theme="success" label="Perbarui" type="submit" form="kunjunganForm" />
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
            
            function sendFile(file) {
                var formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    data: formData,
                    type: "POST",
                    url: "{{ route('kunjungan.uploadImage') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#keterangan').summernote('insertImage', response.url);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal mengunggah gambar: ' + xhr.responseJSON.message, 'error');
                    }
                });
            }

            $('#keterangan').summernote({
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
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        sendFile(files[0]);
                    },
                    onMediaDelete: function($target) {
                        const imageUrl = $target[0].src;

                        if (imageUrl.startsWith('{{ url('') }}')) {
                            $.ajax({
                                data: {
                                    src: imageUrl,
                                    _token: '{{ csrf_token() }}'
                                },
                                type: "POST",
                                url: "{{ route('kunjungan.deleteImage') }}",
                                cache: false,
                                success: function(response) {
                                    console.log(response.success);
                                },
                                error: function(xhr) {
                                    console.error('Gagal menghapus gambar:', xhr.responseJSON ? xhr.responseJSON.message : 'Error tidak diketahui');
                                }
                            });
                        }
                    }
                }
            });

            const initialPelangganId = '{{ $kunjungan->kd_pelanggan }}';
            const initialPesananId = '{{ $kunjungan->kd_pesanan }}';
            const pesananSelect = $('#kd_pesanan');

            function loadPesananForPelanggan(pelangganId, selectedPesananId = null) {
                if (!pelangganId) {
                    pesananSelect.empty().append('<option value="" disabled selected>Pilih Pelanggan terlebih dahulu</option>').prop('disabled', true).trigger('change');
                    return;
                }
                
                pesananSelect.empty().append('<option value="" disabled selected>Memuat...</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ url("/get-pesanan-by-pelanggan") }}/' + pelangganId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        pesananSelect.prop('disabled', false);
                        pesananSelect.empty().append('<option value="" disabled selected>Pilih Pesanan...</option>');

                        if (data.length > 0) {
                            $.each(data, function (key, value) {
                                pesananSelect.append('<option value="' + value.kd_pesanan + '">' + value.deskripsi_pesanan + '</option>');
                            });

                            if (selectedPesananId) {
                                pesananSelect.val(selectedPesananId).trigger('change');
                            }
                        } else {
                            pesananSelect.empty().append('<option value="" disabled selected>Tidak ada pesanan</option>');
                        }
                    },
                    error: function () {
                        pesananSelect.empty().append('<option value="" disabled selected>Gagal memuat data</option>');
                    }
                });
            }

            $('#kd_pelanggan').on('change', function () {
                const selectedPelanggan = $(this).val();
                loadPesananForPelanggan(selectedPelanggan);
            });

            if (initialPelangganId) {
                loadPesananForPelanggan(initialPelangganId, initialPesananId);
            }
        });
    </script>
@endsection

