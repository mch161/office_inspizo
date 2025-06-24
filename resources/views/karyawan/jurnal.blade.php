@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="flex justify-content-between">
        <h1>Jurnal</h1>

    </div>
@stop

@section('js')
    <script>
        import DataTable from 'datatables.net-dt';
        let table = new DataTable('#myTable', {
            // config options...
        });
    </script>
@stop

@section('content')

    <x-adminlte-modal id="modalPurple" title="Theme Purple" theme="purple" icon="fas fa-bolt" size='lg' disable-animations>
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

    {{-- Compressed with style options / fill data using the plugin config --}}
    {{-- <x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config"
    striped hoverable bordered compressed/> --}}
@stop

@section('css')
