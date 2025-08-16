@extends('adminlte::page')

@section('title', 'Pengajuan Izin')

@section('content_header')
    <h1>Tabel Izin</h1>
@endsection

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('css')
    <style>
        .card {
            border-radius: .5rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        #IzinTable th,
        #IzinTable td {
            vertical-align: middle !important;
            text-align: center;
        }

        #IzinTable .keterangan-column {
            text-align: left;
            max-width: 300px;
            white-space: normal;
        }

        .izin-image {
            width: 150px;
            height: auto;
            border-radius: .5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .izin-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: .5rem;
        }

        .btn-sm i {
            margin-right: .25rem;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-3 float-right">
            <a href="{{ route('izin.form') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buat Pengajuan Izin
            </a>
        </div>
        <table id="IzinTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="5%" class="text-left">No</th>
                    <th class="text-left">Karyawan</th>
                    <th class="text-left">Jenis</th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left">Jam</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-left">Foto</th>
                    <th width="10%" class="text-left">Status</th>
                    @if (Auth::user()->role == 'superadmin')
                        <th width="20%" class="text-left">Aksi</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @if(isset($izin))
                    @foreach ($izin as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->dibuat_oleh }}</td>
                            <td>{{ $item->jenis }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d F Y') }}</td>
                            <td>{{ $item->jam }}</td>
                            <td class="keterangan-column">{!! $item->keterangan !!}</td>
                            <td>
                                @if($item->foto)
                                    <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                        data-src="{{ asset('storage/images/izin/' . $item->foto) }}">
                                        <img class="izin-image" src="{{ asset('storage/images/izin/' . $item->foto) }}"
                                            alt="Foto Izin">
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->status == '1')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif ($item->status == '2')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            @if (auth()->user()->role == 'superadmin')
                                <td>
                                    <div class="action-buttons">
                                        @if($item->status == '0')
                                            <form class="approve-form" action="{{ route('izin.update', $item) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                            </form>
                                            <form class="reject-form" action="{{ route('izin.update', $item) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                        @endif
                                        <form class="delete-form" action="{{ route('izin.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>


<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid" alt="Izin Image">
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#IzinTable').DataTable({
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

        function setupFormConfirmation(formClass, title, text, confirmButtonColor) {
            $('.' + formClass).on('submit', function (e) {
                e.preventDefault();
                let form = this;
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#007bff',
                    confirmButtonText: 'Ya, lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        }

        setupFormConfirmation('approve-form', 'Anda yakin?', 'Setujui pengajuan izin ini?', '#28a745');
        setupFormConfirmation('reject-form', 'Anda yakin?', 'Tolak pengajuan izin ini?', '#dc3545');
        setupFormConfirmation('delete-form', 'Anda yakin?', 'Data yang dihapus tidak dapat dikembalikan!', '#dc3545');

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