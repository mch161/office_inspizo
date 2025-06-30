@extends('adminlte::page')

@section('title', 'Keuangan')

@section('content_header')
<h1>Keuangan</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('content')
<table id="KeuanganTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Kotak</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($keuangans as $keuangan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $keuangan->jenis }}</td>
                <td>{{ $keuangan->status }}</td>
                <td>{{ $keuangan->masuk }}</td>
                <td>{{ $keuangan->keluar }}</td>
                <td>{{ $keuangan->kotak }}</td>
                <td>{{ $keuangan->kategori }}</td>
                <td>{{ $keuangan->keterangan }}</td>
                <td>
                    <form action="{{ route('keuangan.destroy', $keuangan->kd_keuangan) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm tombol-hapus">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#KeuanganTable').DataTable();
    });

</script>
@endsection