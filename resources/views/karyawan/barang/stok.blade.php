@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
<h1>Stok</h1>
@stop

@section('plugins.Sweetalert2', true)


@section('css')
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

</style>

@endsection

