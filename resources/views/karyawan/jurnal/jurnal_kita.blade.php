@extends('adminlte::page')

@section('title', 'Jurnal Kita')

@section('content_header')
<h1>Jurnal Kita</h1>
@stop

@section('css')

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table id="JurnalTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="100px">Tanggal</th>
                    <th width="100px">Jam</th>
                    <th width="150px">Nama Karyawan</th>
                    <th>Isi Jurnal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jurnals as $jurnal)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $jurnal->tanggal }}</td>
                        <td>{{ $jurnal->jam }}</td>
                        <td>{{ $jurnal->dibuat_oleh }}</td>
                        <td>{!! $jurnal->isi_jurnal !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#JurnalTable').DataTable({
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
</script>
@stop