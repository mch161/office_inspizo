@extends('adminlte::page')

@section('title', 'Reimburse')

@section('content_header')
<h1>Reimburse Table</h1>
@stop

@section('content')
<table id="ReimburseTable" class="table table-bordered table-striped">
    <thead>
        <tr class="table-primary">
            <th width="5%">No</th>
            <th>Keterangan</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th width="150px" rigth>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reimburse as $reimburse)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $reimburse->keterangan }}</td>
                <td>{{ $reimburse->created_at }}</td>
                <td>{{ $reimburse->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#ReimburseTable').DataTable({
            scrollX: true,
            pageLength: 5,
            lengthChange: false,
            scrollCollapse: true,
            scrollY: '200px'
        });
    })

</script>
@endsection