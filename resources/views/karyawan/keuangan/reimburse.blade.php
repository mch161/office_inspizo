@extends('adminlte::page')

@section('title', 'Reimburse')

@section('content_header')
<h1>Table Reimburse</h1>
@stop
{{-- Include SweetAlert2 and DataTables plugins --}}
@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('css')
{{-- Custom styles for a polished table appearance --}}
<style>
    .card {
        border-radius: .5rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
    }

    #ReimburseTable th,
    #ReimburseTable td {
        vertical-align: middle !important;
        text-align: center;
    }

    #ReimburseTable .keterangan-column {
        text-align: left;
        max-width: 300px;
        white-space: normal;
    }

    .reimburse-image {
        width: 150px;
        height: auto;
        border-radius: .5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .reimburse-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-sm i {
        margin-right: .25rem;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table id="ReimburseTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="5%" class="text-left">ID</th>
                    <th class="text-left">Nominal</th>
                    <th class="text-left">Foto</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-left">Tanggal</th>
                    <th width="10%" class="text-left">Status</th>
                    <th width="15%" class="text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reimburse as $item)
                    <tr>
                        <td class="text-left">{{ $loop->iteration }}</td>
                        <td class="text-left">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td class="text-left">
                            @if ($item->foto)
                                <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                    data-src="{{ asset('storage/images/reimburse/' . $item->foto) }}">
                                    <img class="reimburse-image" src="{{ asset('storage/images/reimburse/' . $item->foto) }}"
                                        alt="Foto Reimburse">
                                </a>
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                        <td class="keterangan-column text-left">{!! $item->keterangan !!}</td>
                        <td class="text-left">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                        <td class="text-left">
                            @if ($item->status == '0')
                                <span class="badge badge-info">Menunggu</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </td>
                        <td class="text-left">
                            <form class="action-form" action="{{ route('reimburse.update', $item->kd_reimburse) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                @if ($item->status == '0')
                                    <input type="hidden" name="status" value="1">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Selesai
                                    </button>
                                @else
                                    <input type="hidden" name="status" value="0">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Batalkan
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid" alt="Reimburse Image">
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#ReimburseTable').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
            scrollX: true,
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

        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });

        $('.action-form').on('submit', function (e) {
            e.preventDefault();
            let form = this;
            let isCompleting = $(this).find('input[name="status"]').val() == '1';

            Swal.fire({
                title: 'Anda yakin?',
                text: isCompleting ? "Tandai reimburse ini sebagai selesai?" : "Batalkan status selesai reimburse ini?",
                icon: isCompleting ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#007bff',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
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
            });
            Toast.fire({
                icon: 'error',
                text: '{{ session('error') }}',
            })
        @endif
    });
</script>
@stop