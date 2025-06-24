@extends('adminlte::page')
  
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
@endsection

@section('content')
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