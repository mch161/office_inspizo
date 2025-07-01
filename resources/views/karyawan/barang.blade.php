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
                <img src="{{ asset('storage/images/barang/'.$b->foto) }}" alt="{{ $b -> nama_barang}}" width="50%">
                <div class="info-box-content">
                    <span class="info-box-text">{{ $b->nama_barang }}</span>
                    <span class="info-box-number">Harga: {{ "Rp" .number_format($b->harga,2,',','.') }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection