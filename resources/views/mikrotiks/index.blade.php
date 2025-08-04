@extends('layouts.app')

@section('title', 'Kelola MikroTik')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-router"></i> Kelola MikroTik</h1>
                <p class="text-muted">Tambah, edit, dan hapus perangkat MikroTik</p>
            </div>
            <a href="{{ route('mikrotiks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah MikroTik
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if($mikrotiks->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>IP Address</th>
                                <th>Port</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mikrotiks as $mikrotik)
                            <tr>
                                <td><strong>{{ $mikrotik->name }}</strong></td>
                                <td>{{ $mikrotik->ip_address }}</td>
                                <td>{{ $mikrotik->port }}</td>
                                <td>{{ $mikrotik->username }}</td>
                                <td>
                                    <span class="badge bg-{{ $mikrotik->is_active ? 'success' : 'danger' }}">
                                        {{ $mikrotik->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('monitor', $mikrotik) }}" class="btn btn-sm btn-success" title="Monitor">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('mikrotiks.edit', $mikrotik) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('mikrotiks.destroy', $mikrotik) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus {{ $mikrotik->name }}?')">
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
        @else
        <div class="alert alert-info">
            <h4><i class="bi bi-info-circle"></i> Belum Ada MikroTik</h4>
            <p>Belum ada perangkat MikroTik yang dikonfigurasi. Klik tombol "Tambah MikroTik" untuk menambahkan perangkat pertama Anda.</p>
        </div>
        @endif
    </div>
</div>
@endsection