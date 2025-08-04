@extends('layouts.app')

@section('title', 'Edit User - ' . $user['name'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-person-gear"></i> Edit User</h1>
                <p class="text-muted">{{ $mikrotik->name }} - User: {{ $user['name'] }}</p>
            </div>
            <a href="{{ route('mikrotik-users.index', $mikrotik) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person-gear"></i> Edit Data User</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('mikrotik-users.update', [$mikrotik, $user['.id']]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username', $user['name']) }}" 
                               placeholder="Masukkan username">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="group" class="form-label">Group *</label>
                        <select class="form-select @error('group') is-invalid @enderror" id="group" name="group">
                            <option value="">Pilih Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group['name'] }}" 
                                        {{ (old('group', $user['group']) == $group['name']) ? 'selected' : '' }}>
                                    {{ $group['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            @php
                                $isDisabled = isset($user['disabled']) && $user['disabled'] === 'true';
                            @endphp
                            <input class="form-check-input" type="checkbox" id="disabled" name="disabled" value="1" 
                                   {{ old('disabled', $isDisabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="disabled">
                                Nonaktifkan user (disabled)
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('mikrotik-users.index', $mikrotik) }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-info-circle"></i> Info User</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>{{ $user['.id'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Group Saat Ini:</strong></td>
                        <td><span class="badge bg-info">{{ $user['group'] }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $isDisabled ? 'danger' : 'success' }}">
                                {{ $isDisabled ? 'Nonaktif' : 'Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Last Login:</strong></td>
                        <td>{{ $user['last-logged-in'] ?? 'Belum pernah' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection