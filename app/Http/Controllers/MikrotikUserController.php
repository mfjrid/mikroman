<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikUserController extends Controller
{
    public function index(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);
        $users = $service->getUsers();
        $groups = $service->getUserGroups();

        return view('mikrotik-users.index', compact('mikrotik', 'users', 'groups'));
    }

    public function create(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);
        $groups = $service->getUserGroups();

        return view('mikrotik-users.create', compact('mikrotik', 'groups'));
    }

    public function store(Request $request, Mikrotik $mikrotik)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'group' => 'required|string',
            'disabled' => 'boolean'
        ]);

        $service = new MikrotikService($mikrotik);

        $result = $service->createUser(
            $request->username,
            $request->password,
            $request->group,
            $request->boolean('disabled')
        );

        if ($result) {
            return redirect()->route('mikrotik-users.index', $mikrotik)
                ->with('success', 'User berhasil ditambahkan!');
        } else {
            return back()->with('error', 'Gagal menambahkan user. Periksa koneksi MikroTik.');
        }
    }

    public function edit(Mikrotik $mikrotik, $userId)
    {
        $service = new MikrotikService($mikrotik);
        $users = $service->getUsers();
        $groups = $service->getUserGroups();

        $user = collect($users)->firstWhere('.id', $userId);

        if (!$user) {
            return redirect()->route('mikrotik-users.index', $mikrotik)
                ->with('error', 'User tidak ditemukan.');
        }

        return view('mikrotik-users.edit', compact('mikrotik', 'user', 'groups'));
    }

    public function update(Request $request, Mikrotik $mikrotik, $userId)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:3',
            'group' => 'required|string',
            'disabled' => 'boolean'
        ]);

        $service = new MikrotikService($mikrotik);

        $updateData = [
            'name' => $request->username,
            'group' => $request->group,
            'disabled' => $request->boolean('disabled')
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $result = $service->updateUser($userId, $updateData);

        if ($result) {
            return redirect()->route('mikrotik-users.index', $mikrotik)
                ->with('success', 'User berhasil diupdate!');
        } else {
            return back()->with('error', 'Gagal mengupdate user. Periksa koneksi MikroTik.');
        }
    }

    public function destroy(Mikrotik $mikrotik, $userId)
    {
        $service = new MikrotikService($mikrotik);
        $result = $service->deleteUser($userId);

        if ($result) {
            return redirect()->route('mikrotik-users.index', $mikrotik)
                ->with('success', 'User berhasil dihapus!');
        } else {
            return back()->with('error', 'Gagal menghapus user. Periksa koneksi MikroTik.');
        }
    }

    public function toggleStatus(Mikrotik $mikrotik, $userId)
    {
        $service = new MikrotikService($mikrotik);
        $users = $service->getUsers();

        $user = collect($users)->firstWhere('.id', $userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.']);
        }

        $isDisabled = isset($user['disabled']) && $user['disabled'] === 'true';

        if ($isDisabled) {
            $result = $service->enableUser($userId);
            $message = 'User berhasil diaktifkan!';
        } else {
            $result = $service->disableUser($userId);
            $message = 'User berhasil dinonaktifkan!';
        }

        return response()->json([
            'success' => $result,
            'message' => $result ? $message : 'Gagal mengubah status user.'
        ]);
    }
}
