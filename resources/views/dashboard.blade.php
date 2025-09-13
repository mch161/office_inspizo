@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Chartjs', true)

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPesananAktif }}</h3>
                    <p>Pesanan Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('pesanan.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @can('superadmin')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                        <p>Pendapatan Bulan Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="{{ route('keuangan.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endcan
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPelanggan }}</h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('pelanggan.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalAgendaHariIni }}</h3>
                    <p>Agenda Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('agenda.index') }}" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Status Pesanan</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusPesananChart"></canvas>
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
            type: 'bar',
            data: chartData,
            options: chartOptions
        });

        var statusChartData = {
            labels: @json($pesananStatusLabels),
            datasets: [{
                data: @json($pesananStatusData),
                backgroundColor: @json($pesananStatusColors),
            }]
        };

        var statusChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
        };

        var statusCtx = document.getElementById('statusPesananChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: statusChartData,
            options: statusChartOptions
        });
    });
</script>
@stop