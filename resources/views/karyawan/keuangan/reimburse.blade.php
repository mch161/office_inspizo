@extends('adminlte::page')

@section('title', 'Reimburse')

@section('plugins.sweetalert2', true)

@section('content_header')
<h1>Reimburse Table</h1>
@stop

@section('content')
<table id="ReimburseTable" class="table table-bordered table-striped">
    <thead>
        <tr class="table-primary">
            <th width="5%">No</th>
            <th>Nominal</th>
            <th>Foto</th>
            <th>Keterangan</th>
            <th>Tanggal</th>
            <th width="50px">Status</th>
            <th width="150px" rigth>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reimburse as $reimburse)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $reimburse->nominal }}</td>
                <td>{{ $reimburse->foto }}</td>
                <td>{{ $reimburse->keterangan }}</td>
                <td>{{ $reimburse->tanggal }}</td>
                <td>
                    @if ($reimburse->status == '0')
                        <span class="badge badge-danger">Belum Selesai</span>
                    @else
                        <span class="badge badge-success">Selesai</span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('reimburse.update', $reimburse->kd_reimburse) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if ($reimburse->status == '0')
                            <button type="submit" class="btn btn-primary tombol-selesai" name="status" value="1">Tandai
                                Selesai</button>
                        @else
                            <button type="submit" class="btn btn-danger tombol-batalkan" name="status"
                                value="0">Batalkan</button>
                        @endif
                    </form>
                </td>
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
        @if (session()->has('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'success',
                text: '{{ session('success') }}',
            })
        @endif
        @if (session()->has('error'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'error',
                text: '{{ session('error') }}',
            })
        @endif
    });
</script>
@endsection