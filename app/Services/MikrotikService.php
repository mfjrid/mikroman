<?php

namespace App\Services;

use App\Models\Mikrotik;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Exception;

class MikrotikService
{
    protected $client;
    protected $mikrotik;

    public function __construct(Mikrotik $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function connect()
    {
        try {
            // Konfigurasi koneksi
            $config = new Config([
                'host' => $this->mikrotik->ip_address,
                'user' => $this->mikrotik->username,
                'pass' => $this->mikrotik->password,
                'port' => $this->mikrotik->port,
                'timeout' => 5, // timeout 5 detik
            ]);

            $this->client = new Client($config);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSystemResource()
    {
        if (!$this->connect()) {
            return null;
        }

        try {
            $response = $this->client->query('/system/resource/print')->read();
            return $response[0] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getInterfaces()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/interface/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getActiveConnections()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/ip/firewall/connection/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getWirelessClients()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/interface/wireless/registration-table/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getDHCPLeases()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/ip/dhcp-server/lease/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getRoutes()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/ip/route/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getHotspotUsers()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/ip/hotspot/active/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getCPUUsage()
    {
        if (!$this->connect()) {
            return 0;
        }

        try {
            $response = $this->client->query('/system/resource/print')->read();
            $cpuLoad = $response[0]['cpu-load'] ?? '0%';
            return (int) str_replace('%', '', $cpuLoad);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getMemoryUsage()
    {
        if (!$this->connect()) {
            return ['used' => 0, 'total' => 0, 'percentage' => 0];
        }

        try {
            $response = $this->client->query('/system/resource/print')->read();

            $totalMemory = $this->parseBytes($response[0]['total-memory'] ?? '0');
            $freeMemory = $this->parseBytes($response[0]['free-memory'] ?? '0');
            $usedMemory = $totalMemory - $freeMemory;

            return [
                'used' => $usedMemory,
                'total' => $totalMemory,
                'percentage' => $totalMemory > 0 ? round(($usedMemory / $totalMemory) * 100, 2) : 0,
                'used_formatted' => $this->formatBytes($usedMemory),
                'total_formatted' => $this->formatBytes($totalMemory),
            ];
        } catch (Exception $e) {
            return ['used' => 0, 'total' => 0, 'percentage' => 0];
        }
    }

    public function getUptime()
    {
        if (!$this->connect()) {
            return 'Unknown';
        }

        try {
            $response = $this->client->query('/system/resource/print')->read();
            return $response[0]['uptime'] ?? 'Unknown';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function reboot()
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $this->client->query('/system/reboot')->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Helper method untuk parsing bytes dari format MikroTik (contoh: "256.0MiB")
    private function parseBytes($str)
    {
        $units = ['B' => 1, 'KiB' => 1024, 'MiB' => 1048576, 'GiB' => 1073741824];

        foreach ($units as $unit => $multiplier) {
            if (strpos($str, $unit) !== false) {
                $number = (float) str_replace($unit, '', $str);
                return $number * $multiplier;
            }
        }

        return (float) $str;
    }

    // Helper method untuk format bytes ke format yang readable
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }


    // ========== USER MANAGEMENT METHODS ==========

    public function getUsers()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/user/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createUser($username, $password, $group = 'full', $disabled = false)
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $query = new Query('/user/add');
            $query->equal('name', $username)
                ->equal('password', $password)
                ->equal('group', $group)
                ->equal('disabled', $disabled ? 'yes' : 'no');

            $this->client->query($query)->read();
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateUser($userId, $data)
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $params = ['.id' => $userId];

            if (isset($data['name'])) {
                $params['name'] = $data['name'];
            }
            if (isset($data['password']) && !empty($data['password'])) {
                $params['password'] = $data['password'];
            }
            if (isset($data['group'])) {
                $params['group'] = $data['group'];
            }
            if (isset($data['disabled'])) {
                $params['disabled'] = $data['disabled'] ? 'yes' : 'no';
            }

            $this->client->query('/user/set', $params)->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteUser($userId)
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $this->client->query('/user/remove', ['.id' => $userId])->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserGroups()
    {
        if (!$this->connect()) {
            return [];
        }

        try {
            return $this->client->query('/user/group/print')->read();
        } catch (Exception $e) {
            return [];
        }
    }

    public function enableUser($userId)
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $this->client->query('/user/enable', ['.id' => $userId])->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function disableUser($userId)
    {
        if (!$this->connect()) {
            return false;
        }

        try {
            $this->client->query('/user/disable', ['.id' => $userId])->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
