<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikPPPController extends Controller
{
    public function index(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);
        $secrets = $service->getPPPSecretsWithStatus();
        $profiles = $service->getPPPProfiles();

        return view('mikrotik-ppp.index', compact('mikrotik', 'secrets', 'profiles'));
    }

    public function create(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);
        $profiles = $service->getPPPProfiles();

        return view('mikrotik-ppp.create', compact('mikrotik', 'profiles'));
    }

    public function store(Request $request, Mikrotik $mikrotik)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'service' => 'nullable|string',
            'profile' => 'nullable|string',
            'local_address' => 'nullable|ip',
            'remote_address' => 'nullable|ip',
            'disabled' => 'boolean'
        ]);

        $service = new MikrotikService($mikrotik);

        $data = [
            'name' => $request->name,
            'password' => $request->password,
            'service' => $request->service,
            'profile' => $request->profile,
            'local-address' => $request->local_address,
            'remote-address' => $request->remote_address,
            'disabled' => $request->boolean('disabled')
        ];

        $result = $service->createPPPSecret($data);

        if ($result) {
            return redirect()->route('mikrotik-ppp.index', $mikrotik)
                ->with('success', 'PPP Secret berhasil ditambahkan!');
        } else {
            return back()->with('error', 'Gagal menambahkan PPP Secret. Periksa koneksi MikroTik.');
        }
    }

    public function edit(Mikrotik $mikrotik, $secretId)
    {
        $service = new MikrotikService($mikrotik);
        $secrets = $service->getPPPSecrets();
        $profiles = $service->getPPPProfiles();

        $secret = collect($secrets)->firstWhere('.id', $secretId);

        if (!$secret) {
            return redirect()->route('mikrotik-ppp.index', $mikrotik)
                ->with('error', 'PPP Secret tidak ditemukan.');
        }

        return view('mikrotik-ppp.edit', compact('mikrotik', 'secret', 'profiles'));
    }

    public function update(Request $request, Mikrotik $mikrotik, $secretId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:3',
            'service' => 'nullable|string',
            'profile' => 'nullable|string',
            'local_address' => 'nullable|ip',
            'remote_address' => 'nullable|ip',
            'disabled' => 'boolean'
        ]);

        $service = new MikrotikService($mikrotik);

        $data = [
            'name' => $request->name,
            'service' => $request->service,
            'profile' => $request->profile,
            'local-address' => $request->local_address,
            'remote-address' => $request->remote_address,
            'disabled' => $request->boolean('disabled')
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $result = $service->updatePPPSecret($secretId, $data);

        if ($result) {
            return redirect()->route('mikrotik-ppp.index', $mikrotik)
                ->with('success', 'PPP Secret berhasil diupdate!');
        } else {
            return back()->with('error', 'Gagal mengupdate PPP Secret. Periksa koneksi MikroTik.');
        }
    }

    public function destroy(Mikrotik $mikrotik, $secretId)
    {
        $service = new MikrotikService($mikrotik);
        $result = $service->deletePPPSecret($secretId);

        if ($result) {
            return redirect()->route('mikrotik-ppp.index', $mikrotik)
                ->with('success', 'PPP Secret berhasil dihapus!');
        } else {
            return back()->with('error', 'Gagal menghapus PPP Secret. Periksa koneksi MikroTik.');
        }
    }

    public function disconnect(Mikrotik $mikrotik, $activeId)
    {
        $service = new MikrotikService($mikrotik);
        $result = $service->disconnectPPPUser($activeId);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'User berhasil didisconnect!' : 'Gagal disconnect user.'
        ]);
    }

    public function refreshStatus(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);
        $secrets = $service->getPPPSecretsWithStatus();

        return response()->json([
            'success' => true,
            'data' => $secrets,
            'count' => count($secrets)
        ]);
    }
}
