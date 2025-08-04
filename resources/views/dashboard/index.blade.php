@extends('layouts.app')

@section('title', 'Dashboard MikroTik')

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="bi bi-speedometer2"></i> Dashboard MikroTik</h1>
        <p class="text-muted">Monitoring dan manajemen perangkat MikroTik Anda</p>
    </div>
</div>

<div class="row">
    @forelse($mikrotiks as $mikrotik)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-router"></i> {{ $mikrotik->name }}
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>IP:</strong> {{ $mikrotik->ip_address }}<br>
                    <strong>Port:</strong> {{ $mikrotik->port }}<br>
                    <strong>User:</strong> {{ $mikrotik->username }}
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('monitor', $mikrotik) }}" class="btn btn-success">
                        <i class="bi bi-eye"></i> Monitor
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="bi bi-info-circle"></i> Tidak ada MikroTik</h4>
            <p>Belum ada perangkat MikroTik yang dikonfigurasi. Silakan tambahkan perangkat MikroTik terlebih dahulu.</p>
            <a href="{{ route('mikrotiks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah MikroTik
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection