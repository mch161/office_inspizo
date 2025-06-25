@extends('adminlte::page')

@section('title', 'Jurnal')

@section('content_header')
    <div class="flex justify-content-between">
        <h1>Jurnal</h1>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#myTable').DataTable();
    });
</script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
@endsection

@section('content')

<x-adminlte-modal id="modalPurple" title="Tambahkan Jurnal" theme="purple" icon="fas fa-clipboard" size='lg'>
    This is a purple theme modal without animations.
</x-adminlte-modal>

<x-adminlte-button label="Tambahkan Jurnal" class="float-right mb-2 bg-blue" data-toggle="modal"
    data-target="#modalPurple" />

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Row 1 Data 1</td>
            <td>Row 1 Data 2</td>
        </tr>
        <tr>
            <td>Row 2 Data 1</td>
            <td>Row 2 Data 2</td>
        </tr>
    </tbody>
</table>

@stop