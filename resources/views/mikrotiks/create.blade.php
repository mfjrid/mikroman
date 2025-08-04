@extends('layouts.app')

@section('title', 'Tambah MikroTik')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-plus-circle"></i> Tambah MikroTik</h1>
                <p class="text-muted">Tambahkan perangkat MikroTik baru untuk monitoring</p>
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
                <form action="{{ route('mikrotiks.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama MikroTik *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Contoh: Router Kantor Utama">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address *</label>
                        <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                               id="ip_address" name="ip_address" value="{{ old('ip_address') }}" 
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
                                       id="username" name="username" value="{{ old('username', 'admin') }}" 
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
                                       id="port" name="port" value="{{ old('port', 8728) }}" 
                                       min="1" max="65535">
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Masukkan password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('mikrotiks.index') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-info-circle"></i> Panduan Setup</h6>
            </div>
            <div class="card-body">
                <h6>1. Aktifkan RouterOS API</h6>
                <div class="bg-light p-2 rounded mb-3">
                    <code>/ip service<br>set api disabled=no<br>set api port=8728</code>
                </div>
                
                <h6>2. Buat User API (Opsional)</h6>
                <div class="bg-light p-2 rounded mb-3">
                    <code>/user add name=api-user password=your-password group=full</code>
                </div>
                
                <h6>3. Firewall (Jika Diperlukan)</h6>
                <div class="bg-light p-2 rounded">
                    <code>/ip firewall filter add chain=input protocol=tcp dst-port=8728 action=accept</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection