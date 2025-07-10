@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
<h1>Change Password</h1>
@stop   

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

@section('content')

<form method="POST" id="ProfileForm">
<x-adminlte-input name="iUser" label="Nama" placeholder="username" label-class="text-lightblue">
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-user text-lightblue"></i>
        </div>
    </x-slot>
</x-adminlte-input>

<x-adminlte-input name="iUser" label="Username" placeholder="username" label-class="text-lightblue">
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-user text-lightblue"></i>
        </div>
    </x-slot>
</x-adminlte-input>

<x-adminlte-input name="iNum" label="Nomor Hp" placeholder="+623456789" type="number"
    igroup-size="sm" min=1 max=10>
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>

<x-adminlte-input name="iUser" label="Email" placeholder="username" label-class="text-lightblue">
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-user text-lightblue"></i>
        </div>
    </x-slot>
</x-adminlte-input>


</form>
@endsection