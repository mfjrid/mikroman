@extends('layouts.app')

@section('title', 'Monitor ' . $mikrotik->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-activity"></i> Monitor: {{ $mikrotik->name }}</h1>
                <p class="text-muted">{{ $mikrotik->ip_address }}:{{ $mikrotik->port }}</p>
            </div>
            <div>
                <a href="{{ route('mikrotik-users.index', $mikrotik) }}" class="btn btn-info me-2">
                    <i class="bi bi-people"></i> User Management
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

@if($system_resource)
<!-- System Resource Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $cpu_usage }}%</h4>
                        <p>CPU Usage</p>
                    </div>
                    <i class="bi bi-cpu fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $memory_usage['percentage'] }}%</h4>
                        <p>Memory Usage</p>
                    </div>
                    <i class="bi bi-memory fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ count($interfaces) }}</h4>
                        <p>Interfaces</p>
                    </div>
                    <i class="bi bi-ethernet fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ count($wireless_clients) }}</h4>
                        <p>WiFi Clients</p>
                    </div>
                    <i class="bi bi-wifi fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Board Name:</strong></td>
                        <td>{{ $system_resource['board-name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Version:</strong></td>
                        <td>{{ $system_resource['version'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Architecture:</strong></td>
                        <td>{{ $system_resource['architecture-name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Uptime:</strong></td>
                        <td>{{ $system_resource['uptime'] ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up"></i> Usage Chart</h5>
            </div>
            <div class="card-body">
                <canvas id="usageChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Interfaces -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-ethernet"></i> Network Interfaces</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>RX</th>
                                <th>TX</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interfaces as $interface)
                            <tr>
                                <td>{{ $interface['name'] ?? 'N/A' }}</td>
                                <td>{{ $interface['type'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($interface['running'] ?? false) ? 'success' : 'danger' }}">
                                        {{ ($interface['running'] ?? false) ? 'Running' : 'Stopped' }}
                                    </span>
                                </td>
                                <td>{{ $interface['rx-byte'] ?? '0' }}</td>
                                <td>{{ $interface['tx-byte'] ?? '0' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DHCP Leases -->
@if(count($dhcp_leases) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-network"></i> DHCP Leases</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>MAC Address</th>
                                <th>Host Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dhcp_leases as $lease)
                            <tr>
                                <td>{{ $lease['address'] ?? 'N/A' }}</td>
                                <td>{{ $lease['mac-address'] ?? 'N/A' }}</td>
                                <td>{{ $lease['host-name'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($lease['status'] ?? '') == 'bound' ? 'success' : 'warning' }}">
                                        {{ $lease['status'] ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@else
<div class="alert alert-danger">
    <h4><i class="bi bi-exclamation-triangle"></i> Koneksi Gagal</h4>
    <p>Tidak dapat terhubung ke MikroTik {{ $mikrotik->name }} ({{ $mikrotik->ip_address }}). Periksa konfigurasi dan pastikan perangkat dapat diakses.</p>
</div>
@endif

@push('scripts')
<script>
// Usage Chart
const ctx = document.getElementById('usageChart').getContext('2d');
const usageChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['CPU Usage', 'Memory Usage', 'Free CPU', 'Free Memory'],
        datasets: [{
            data: [{{ $cpu_usage }}, {{ $memory_usage['percentage'] }}, {{ 100 - $cpu_usage }}, {{ 100 - $memory_usage['percentage'] }}],
            backgroundColor: [
                '#dc3545',
                '#198754',
                '#e9ecef',
                '#e9ecef'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Auto refresh every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush
@endsection