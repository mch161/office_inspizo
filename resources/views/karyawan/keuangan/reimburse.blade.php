@extends('adminlte::page')

@section('title', 'Reimburse')

{{-- Include SweetAlert2 and DataTables plugins --}}
@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('content_header')
<h1>Tabel Reimburse</h1>
@stop

@section('css')
    {{-- Custom styles for better table appearance --}}
    <style>
        #ReimburseTable th,
        #ReimburseTable td {
            vertical-align: middle !important;
            text-align: center;
        }

        #ReimburseTable .keterangan-column {
            text-align: left;
            max-width: 350px;
            /* Limit width of description */
            white-space: normal;
            /* Allow text to wrap */
        }

        .reimburse-image {
            width: 150px;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .reimburse-image:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengajuan Reimburse</h3>
    </div>
    <div class="card-body">
        <table id="ReimburseTable" class="table table-bordered table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th width="5%">No</th>
                    <th>Nominal</th>
                    <th>Foto</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                    <th width="10%">Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reimburse as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        {{-- Format number as Indonesian Rupiah --}}
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>
                            {{-- Make image clickable to open modal --}}
                            <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                data-src="{{ asset('storage/images/reimburse/' . $item->foto) }}">
                                <img class="reimburse-image" src="{{ asset('storage/images/reimburse/' . $item->foto) }}"
                                    alt="Foto Reimburse">
                            </a>
                        </td>
                        {{-- Render HTML from Summernote and align left --}}
                        <td class="keterangan-column">{!! $item->keterangan !!}</td>
                        {{-- Format date to be more readable --}}
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>
                            @if ($item->status == '0')
                                <span class="badge badge-warning">Menunggu</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <form class="action-form" action="{{ route('reimburse.update', $item->kd_reimburse) }}"
                                method="POST">
                                @csrf
                                @method('PUT')

                                @if ($item->status == '0')
                                    {{-- Add a hidden input to hold the status value --}}
                                    <input type="hidden" name="status" value="1">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Selesai
                                    </button>
                                @else
                                    {{-- Add a hidden input for the "cancel" action --}}
                                    <input type="hidden" name="status" value="0">
                                    <button type="submit" class="btn btn-sm btn-secondary">
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Reimburse Image">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#ReimburseTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 10,
        });

        // Handle image popup modal
        $('.image-popup').on('click', function (e) {
            e.preventDefault();
            const imageUrl = $(this).data('src');
            $('#modalImage').attr('src', imageUrl);
        });

        // Handle form submission with SweetAlert confirmation
        $('.action-form').on('submit', function (e) {
            e.preventDefault();
            let form = this;
            let button = $(this).find('button[type="submit"]');
            let isCompleting = button.val() == '1';

            Swal.fire({
                title: 'Anda yakin?',
                text: isCompleting ? "Tandai reimburse ini sebagai selesai?" : "Batalkan status selesai reimburse ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isCompleting ? '#28a745' : '#6c757d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        // Display success/error toasts
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