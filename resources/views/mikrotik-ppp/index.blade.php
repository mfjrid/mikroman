@extends('layouts.app')

@section('title', 'PPP Secrets - ' . $mikrotik->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-shield-lock"></i> PPP Secrets</h1>
                <p class="text-muted">{{ $mikrotik->name }} ({{ $mikrotik->ip_address }})</p>
            </div>
            <div>
                <button id="refreshBtn" class="btn btn-info me-2" onclick="refreshStatus()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
                <a href="{{ route('monitor', $mikrotik) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('mikrotik-ppp.create', $mikrotik) }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Tambah Secret
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="totalSecrets">{{ count($secrets) }}</h4>
                        <p>Total Secrets</p>
                    </div>
                    <i class="bi bi-shield-lock fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="onlineUsers">{{ collect($secrets)->where('is_active', true)->count() }}</h4>
                        <p>Online Users</p>
                    </div>
                    <i class="bi bi-wifi fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="offlineUsers">{{ collect($secrets)->where('is_active', false)->count() }}</h4>
                        <p>Offline Users</p>
                    </div>
                    <i class="bi bi-wifi-off fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="disabledUsers">{{ collect($secrets)->where('disabled', 'true')->count() }}</h4>
                        <p>Disabled Users</p>
                    </div>
                    <i class="bi bi-ban fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($secrets) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-ul"></i> Daftar PPP Secrets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="secretsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Profile</th>
                                <th>Status</th>
                                <th>Connection Info</th>
                                <th>Last Logout</th>
                                <th>Enabled</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secrets as $secret)
                            <tr id="secret-{{ $secret['.id'] }}">
                                <td><strong>{{ $secret['name'] ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $secret['service'] ?? 'any' }}</span>
                                </td>
                                <td>{{ $secret['profile'] ?? 'default' }}</td>
                                <td>
                                    @if($secret['is_active'])
                                        <span class="badge bg-success">
                                            <i class="bi bi-circle-fill"></i> Online
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-circle"></i> Offline
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($secret['is_active'] && $secret['connection_info'])
                                        <small>
                                            <strong>IP:</strong> {{ $secret['connection_info']['address'] ?? 'N/A' }}<br>
                                            <strong>Caller:</strong> {{ $secret['connection_info']['caller-id'] ?? 'N/A' }}<br>
                                            <strong>Uptime:</strong> {{ $secret['connection_info']['uptime'] ?? 'N/A' }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $secret['last-logout'] ?? 'Never' }}</small>
                                </td>
                                <td>
                                    @php
                                        $isDisabled = isset($secret['disabled']) && $secret['disabled'] === 'true';
                                    @endphp
                                    <span class="badge bg-{{ $isDisabled ? 'danger' : 'success' }}">
                                        {{ $isDisabled ? 'Disabled' : 'Enabled' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($secret['is_active'])
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="disconnectUser('{{ $secret['.id'] }}', '{{ $secret['name'] }}')"
                                                    title="Disconnect">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('mikrotik-ppp.edit', [$mikrotik, $secret['.id']]) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('mikrotik-ppp.destroy', [$mikrotik, $secret['.id']]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Yakin ingin menghapus secret {{ $secret['name'] }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
@else
<div class="alert alert-info">
    <h4><i class="bi bi-info-circle"></i> Tidak Ada PPP Secrets</h4>
    <p>Tidak dapat mengambil data PPP secrets dari MikroTik. Periksa koneksi atau tambah secret pertama.</p>
    <a href="{{ route('mikrotik-ppp.create', $mikrotik) }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> Tambah Secret
    </a>
</div>
@endif

@push('scripts')
<script>
function disconnectUser(activeId, username) {
    if (confirm(`Yakin ingin disconnect user ${username}?`)) {
        fetch(`{{ route('mikrotik-ppp.disconnect', [$mikrotik, '__ACTIVE_ID__']) }}`.replace('__ACTIVE_ID__', activeId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshStatus();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }
}

function refreshStatus() {
    const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Refreshing...';

    fetch('{{ route('mikrotik-ppp.refresh', $mikrotik) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal refresh data');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        })
        .finally(() => {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh';
        });
}

// Auto refresh setiap 30 detik
setInterval(refreshStatus, 30000);
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection