@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
    <h1>Barang</h1>
@stop

@section('content')
<div class="container grid">
    @foreach ( $barang as $b )
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="far fa-box"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ $b->nama_barang }}</span>
                    <span>Jumlah: {{ $b->jumlah }}</span>
                    <span class="info-box-number">Harga: {{ $b->harga }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection