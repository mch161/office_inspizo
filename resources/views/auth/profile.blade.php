@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
<h1 class="text-lightblue">My Profile</h1>
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
    <div class="row">
        <div class="col-12">
            {{-- Profile Header --}}
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        {{-- Use a placeholder image, or link to a static asset --}}
                        <img class="profile-user-img img-fluid img-circle"
                             src="https://i.pravatar.cc/150?u=a042581f4e29026704d"
                             alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">Natasha Khaleira</h3>
                    <p class="text-muted text-center">Admin</p>
                    <p class="text-muted text-center" style="font-size: 0.9rem;">Leeds, United Kingdom</p>
                </div>
            </div>

            {{-- Personal Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Information</h3>
                    <div class="card-tools">
                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>First Name</strong>
                            <p class="text-muted">Natashia</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Last Name</strong>
                            <p class="text-muted">Khaleira</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Date of Birth</strong>
                            <p class="text-muted">12-10-1990</p>
                        </div>
                    </div>
                    <hr>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Email Address</strong>
                            <p class="text-muted">info@binary-fusion.com</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Phone Number</strong>
                            <p class="text-muted">(+62) 821 2554-5846</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>User Role</strong>
                            <p class="text-muted">Admin</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Address</h3>
                     <div class="card-tools">
                        <a href="#" class="btn btn-warning btn-sm">Edit</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Country</strong>
                            <p class="text-muted">United Kingdom</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>City</strong>
                            <p class="text-muted">Leeds, East London</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Postal Code</strong>
                            <p class="text-muted">ERT 1254</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


{{-- <form method="POST" id="ProfileForm">
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

<x-adminlte-input name="iNum" label="Nomor Hp" label-clas="text-lightblue" placeholder="+623456789" type="number">
    <x-slot name="prependSlot">
        <div class="input-group-text">
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
</form> --}}
@endsection