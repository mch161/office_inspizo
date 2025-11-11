@extends('adminlte::page')

@section('title', 'Lembur')

@section('content_header')
<h1>Lembur</h1>
@stop

@section('content')
<div class="modal fade" id="modalLembur">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Lembur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="lemburForm" class="form-horizontal">
                    @php $config = ['format' => 'DD-MM-YYYY']; @endphp
                    <x-adminlte-input-date name="tanggal" value="{{ date('d-m-Y') }}" :config="$config"
                        placeholder="Pilih tanggal..." label="Tanggal" igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-calendar-day"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                    @php $config = ['format' => 'HH:mm']; @endphp
                    <x-adminlte-input-date name="jam_mulai" id="mulai" :config="$config" placeholder="Pilih jam..."
                        label="Jam Mulai" igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                    @php $config = ['format' => 'HH:mm']; @endphp
                    <x-adminlte-input-date name="jam_selesai" id="selesai" :config="$config" placeholder="Pilih jam..."
                        label="Jam Selesai" igroup-size="md" required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark"><i class="fas fa-clock"></i></div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-textarea name="keterangan" label="Keterangan" rows=5 igroup-size="sm"
                        placeholder="Masukkan keterangan...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-dark">
                                <i class="fas fa-lg fa-file-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>

                    <div class="col-sm-offset-2 col-sm-10 mt-3">
                        <button type="button" class="btn btn-success" id="saveBtn">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tabel Lembur</h3>
    </div>
    <div class="card-body">
        <div class="mb-3 float-right">
            <button class="btn btn-primary" id="formLembur">
                <i class="fas fa-plus"></i>
                Form Lembur
            </button>
        </div>
        <table id="LemburTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $('#LemburTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('lembur.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'karyawan', name: 'karyawans.nama' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'jam', name: 'jam' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: -1,
            scrollX: true,
            language: {
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data",
                emptyTable: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total entri)",
                search: "Cari:",
                searchPlaceholder: "Cari data..."
            }
        });
        $('#formLembur').on('click', function () {
            $('#saveBtn').text('Kirim');
            $('#lemburForm').trigger("reset");
            $('#modalLembur').modal('show');
        })
        $('#modalLembur').on('click', '#saveBtn', function (e) {
            e.preventDefault();
            var saveButton = $(this);
            saveButton.text('Menyimpan...');

            $.ajax({
                data: $('#lemburForm').serialize(),
                url: "{{ route('lembur.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#lemburForm').trigger("reset");
                    $('#modalLembur').modal('hide');
                    $('#LemburTable').DataTable().ajax.reload(null, false);
                    Swal.fire('Sukses!', data.success, 'success');
                },
                error: function (data) {
                    console.log('Error:', data);
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (data.responseJSON && data.responseJSON.errors) {
                        errorMsg = Object.values(data.responseJSON.errors).map(val => val.join('<br>')).join('<br>');
                    }
                    saveButton.text('Simpan');
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        });
        $('.tombol-hapus').on('click', function (e) {
            e.preventDefault();
            let form = $(this).parents('form');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        })
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