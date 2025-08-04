@extends('layouts.app')

@section('title', 'Edit MikroTik - ' . $mikrotik->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-pencil-square"></i> Edit MikroTik</h1>
                <p class="text-muted">Edit konfigurasi: {{ $mikrotik->name }}</p>
            </div>
            <a href="{{ route('mikrotiks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> Konfigurasi MikroTik</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('mikrotiks.update', $mikrotik) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama MikroTik *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $mikrotik->name) }}" 
                               placeholder="Contoh: Router Kantor Utama">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address *</label>
                        <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                               id="ip_address" name="ip_address" value="{{ old('ip_address', $mikrotik->ip_address) }}" 
                               placeholder="Contoh: 192.168.1.1">
                        @error('ip_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username', $mikrotik->username) }}" 
                                       placeholder="admin">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="port" class="form-label">Port API</label>
                                <input type="number" class="form-control @error('port') is-invalid @enderror" 
                                       id="port" name="port" value="{{ old('port', $mikrotik->port) }}" 
                                       min="1" max="65535">
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $mikrotik->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan monitoring untuk perangkat ini
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('mikrotiks.index') }}" class="btn btn-secondary me-md-2">
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
                <h6><i class="bi bi-shield-check"></i> Test Koneksi</h6>
            </div>
            <div class="card-body">
                <p>Klik tombol di bawah untuk test koneksi ke MikroTik:</p>
                <a href="{{ route('monitor', $mikrotik) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-wifi"></i> Test Koneksi
                </a>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6><i class="bi bi-info-circle"></i> Info Perangkat</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Dibuat:</strong> {{ $mikrotik->created_at->format('d M Y H:i') }}<br>
                    <strong>Update Terakhir:</strong> {{ $mikrotik->updated_at->format('d M Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection