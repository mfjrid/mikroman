@extends('layouts.app')

@section('title', 'User Management - ' . $mikrotik->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-people"></i> User Management</h1>
                <p class="text-muted">{{ $mikrotik->name }} ({{ $mikrotik->ip_address }})</p>
            </div>
            <div>
                <a href="{{ route('monitor', $mikrotik) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('mikrotik-users.create', $mikrotik) }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Tambah User
                </a>
            </div>
        </div>
    </div>
</div>

@if(count($users) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person-lines-fill"></i> Daftar User MikroTik</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Group</th>
                                <th>Status</th>
                                <th>Last Logged In</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user['name'] ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user['group'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $isDisabled = isset($user['disabled']) && $user['disabled'] === 'true';
                                    @endphp
                                    <span class="badge bg-{{ $isDisabled ? 'danger' : 'success' }}">
                                        {{ $isDisabled ? 'Nonaktif' : 'Aktif' }}
                                    </span>
                                </td>
                                <td>{{ $user['last-logged-in'] ?? 'Belum pernah' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-{{ $isDisabled ? 'success' : 'warning' }}" 
                                                onclick="toggleUserStatus('{{ $user['.id'] }}', '{{ $user['name'] }}', {{ $isDisabled ? 'false' : 'true' }})"
                                                title="{{ $isDisabled ? 'Aktifkan' : 'Nonaktifkan' }}">
                                            <i class="bi bi-{{ $isDisabled ? 'play' : 'pause' }}-fill"></i>
                                        </button>
                                        <a href="{{ route('mikrotik-users.edit', [$mikrotik, $user['.id']]) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user['name'] !== 'admin')
                                        <form action="{{ route('mikrotik-users.destroy', [$mikrotik, $user['.id']]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Yakin ingin menghapus user {{ $user['name'] }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
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
    <h4><i class="bi bi-info-circle"></i> Tidak Ada User</h4>
    <p>Tidak dapat mengambil data user dari MikroTik. Periksa koneksi atau tambah user pertama.</p>
    <a href="{{ route('mikrotik-users.create', $mikrotik) }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> Tambah User
    </a>
</div>
@endif

@push('scripts')
<script>
function toggleUserStatus(userId, username, disable) {
    if (confirm(`Yakin ingin ${disable === 'true' ? 'menonaktifkan' : 'mengaktifkan'} user ${username}?`)) {
        fetch(`{{ route('mikrotik-users.toggle-status', [$mikrotik, '__USER_ID__']) }}`.replace('__USER_ID__', userId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }
}
</script>
@endpush
@endsection