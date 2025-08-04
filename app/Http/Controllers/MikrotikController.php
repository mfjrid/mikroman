<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MikrotikController extends Controller
{
    public function index()
    {
        $mikrotiks = Mikrotik::all();
        return view('mikrotiks.index', compact('mikrotiks'));
    }

    public function create()
    {
        return view('mikrotiks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        Mikrotik::create([
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'username' => $request->username,
            'password' => $request->password,
            'port' => $request->port,
            'is_active' => true,
        ]);

        return redirect()->route('mikrotiks.index')->with('success', 'MikroTik berhasil ditambahkan!');
    }

    public function show(Mikrotik $mikrotik)
    {
        return view('mikrotiks.show', compact('mikrotik'));
    }

    public function edit(Mikrotik $mikrotik)
    {
        return view('mikrotiks.edit', compact('mikrotik'));
    }

    public function update(Request $request, Mikrotik $mikrotik)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'username' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        $updateData = [
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'username' => $request->username,
            'port' => $request->port,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $mikrotik->update($updateData);

        return redirect()->route('mikrotiks.index')->with('success', 'MikroTik berhasil diupdate!');
    }

    public function destroy(Mikrotik $mikrotik)
    {
        $mikrotik->delete();
        return redirect()->route('mikrotiks.index')->with('success', 'MikroTik berhasil dihapus!');
    }
}
