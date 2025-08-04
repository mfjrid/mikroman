<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $mikrotiks = Mikrotik::where('is_active', true)->get();
        return view('dashboard.index', compact('mikrotiks'));
    }

    public function monitor(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);

        $data = [
            'mikrotik' => $mikrotik,
            'system_resource' => $service->getSystemResource(),
            'interfaces' => $service->getInterfaces(),
            'wireless_clients' => $service->getWirelessClients(),
            'dhcp_leases' => $service->getDHCPLeases(),
            'cpu_usage' => $service->getCPUUsage(),
            'memory_usage' => $service->getMemoryUsage(),
        ];

        return view('dashboard.monitor', $data);
    }

    public function getStats(Mikrotik $mikrotik)
    {
        $service = new MikrotikService($mikrotik);

        return response()->json([
            'cpu_usage' => $service->getCPUUsage(),
            'memory_usage' => $service->getMemoryUsage(),
            'interfaces' => count($service->getInterfaces()),
            'wireless_clients' => count($service->getWirelessClients()),
            'dhcp_leases' => count($service->getDHCPLeases()),
        ]);
    }
}
