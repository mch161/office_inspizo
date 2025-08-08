@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Chartjs', true)

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    @if (Auth::guard('karyawan')->user()->role == 'superadmin')
        <div class="row row-cols-1 row-cols-md-4 g-4">
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
    @endif

        <div class="col">
            <x-adminlte-small-box title="{{ $totalPelanggan }}" text="Total Pelanggan" icon="fas fa-users text-primary"
                theme="primary" url="{{ route('pelanggan.index') }}" url-text="Lihat Detail" />
        </div>
        <div class="col">
            <x-adminlte-small-box title="{{ $totalPesananAktif }}" text="Pesanan Aktif"
                icon="fas fa-shopping-cart text-warning" theme="warning" url="{{ route('pesanan.index') }}"
                url-text="Lihat Detail" />
        </div>
        <div class="col">
            <x-adminlte-small-box title="{{ $totalAgendaHariIni }}" text="Agenda Hari Ini"
                icon="fas fa-calendar-day text-success" theme="success" url="{{ route('agenda.index') }}"
                url-text="Lihat Detail" />
        </div>
        @if (Auth::guard('karyawan')->user()->role == 'superadmin')
            <div class="col">
                <x-adminlte-small-box title="Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}"
                    text="Pendapatan Bulan Ini" icon="fas fa-dollar-sign text-info" theme="info"
                    url="{{ route('keuangan.index') }}" url-text="Lihat Detail" />
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Pesanan 6 Bulan Terakhir</h3>
                </div>
                <div class="card-body">
                    <canvas id="pesananChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Agenda Terdekat</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($agendaTerdekat as $agenda)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-check" style="color: {{ $agenda->color ?? '#007bff' }};"></i>
                                    <strong>{{ $agenda->title }}</strong>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    {{ \Carbon\Carbon::parse($agenda->start)->format('d M, H:i') }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center">
                                Tidak ada agenda akan datang.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('agenda.index') }}">Lihat Semua Agenda</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        var chartData = {
            labels: @json($labels),
            datasets: [{
                label: 'Jumlah Pesanan',
                data: @json($data),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        var chartOptions = {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
        };

        var ctx = document.getElementById('pesananChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: chartOptions
        });
    });
</script>
@stop