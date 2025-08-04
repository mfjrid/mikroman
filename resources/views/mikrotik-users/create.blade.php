@extends('layouts.app')

@section('title', 'Tambah User - ' . $mikrotik->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-person-plus"></i> Tambah User</h1>
                <p class="text-muted">{{ $mikrotik->name }} ({{ $mikrotik->ip_address }})</p>
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
                <h5><i class="bi bi-person-gear"></i> Data User Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('mikrotik-users.store', $mikrotik) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" 
                               placeholder="Masukkan username">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Masukkan password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="group" class="form-label">Group *</label>
                        <select class="form-select @error('group') is-invalid @enderror" id="group" name="group">
                            <option value="">Pilih Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group['name'] }}" {{ old('group') == $group['name'] ? 'selected' : '' }}>
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
                            <input class="form-check-input" type="checkbox" id="disabled" name="disabled" value="1" 
                                   {{ old('disabled') ? 'checked' : '' }}>
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
                <h6><i class="bi bi-info-circle"></i> Info Group</h6>
            </div>
            <div class="card-body">
                @if(count($groups) > 0)
                    @foreach($groups as $group)
                        <div class="mb-2">
                            <strong>{{ $group['name'] }}</strong><br>
                            <small class="text-muted">
                                Policy: {{ $group['policy'] ?? 'N/A' }}
                            </small>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <p class="text-muted">Data group tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection