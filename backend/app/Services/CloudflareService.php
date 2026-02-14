<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    private string $apiKey;
    private string $email;
    private string $baseUrl = 'https://api.cloudflare.com/client/v4';

    public function __construct()
    {
        $this->apiKey = config('domains.cloudflare.api_key');
        $this->email = config('domains.cloudflare.email');
    }

    /**
     * Add a domain (zone) to Cloudflare.
     */
    public function addZone(string $domain): array
    {
        $response = $this->post('/zones', [
            'name' => $domain,
            'jump_start' => true,
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'errors' => $response['errors'] ?? [],
                'message' => $this->extractError($response),
            ];
        }

        $zone = $response['result'];

        return [
            'success' => true,
            'zone_id' => $zone['id'],
            'name' => $zone['name'],
            'status' => $zone['status'],
            'name_servers' => $zone['name_servers'] ?? [],
        ];
    }

    /**
     * Get zone details by zone ID.
     */
    public function getZone(string $zoneId): array
    {
        $response = $this->get("/zones/{$zoneId}");

        if (!$response['success']) {
            return ['success' => false, 'message' => $this->extractError($response)];
        }

        $zone = $response['result'];

        return [
            'success' => true,
            'zone_id' => $zone['id'],
            'name' => $zone['name'],
            'status' => $zone['status'],
            'name_servers' => $zone['name_servers'] ?? [],
            'original_name_servers' => $zone['original_name_servers'] ?? [],
        ];
    }

    /**
     * Find a zone by domain name.
     */
    public function getZoneByName(string $domain): ?array
    {
        $response = $this->get('/zones', ['name' => $domain]);

        if (!$response['success'] || empty($response['result'])) {
            return null;
        }

        $zone = $response['result'][0];

        return [
            'zone_id' => $zone['id'],
            'name' => $zone['name'],
            'status' => $zone['status'],
            'name_servers' => $zone['name_servers'] ?? [],
        ];
    }

    /**
     * Add a DNS record to a zone.
     */
    public function addDnsRecord(string $zoneId, string $type, string $name, string $content, bool $proxied = true): array
    {
        $data = [
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'proxied' => $proxied,
        ];

        if ($type === 'MX') {
            $data['priority'] = 10;
            $data['proxied'] = false;
        }

        if (in_array($type, ['A', 'AAAA', 'CNAME'])) {
            $data['ttl'] = 1; // Auto
        }

        $response = $this->post("/zones/{$zoneId}/dns_records", $data);

        if (!$response['success']) {
            return [
                'success' => false,
                'message' => $this->extractError($response),
            ];
        }

        return [
            'success' => true,
            'record' => $response['result'],
        ];
    }

    /**
     * Set SSL mode for a zone.
     */
    public function setSslMode(string $zoneId, string $mode = 'full'): bool
    {
        $response = $this->patch("/zones/{$zoneId}/settings/ssl", [
            'value' => $mode,
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Check zone activation status.
     */
    public function checkActivation(string $zoneId): string
    {
        $response = $this->get("/zones/{$zoneId}");

        if (!$response['success']) {
            return 'unknown';
        }

        return $response['result']['status'] ?? 'unknown';
    }

    /**
     * Trigger an activation check for a zone.
     */
    public function triggerActivationCheck(string $zoneId): bool
    {
        $response = $this->put("/zones/{$zoneId}/activation_check");

        return $response['success'] ?? false;
    }

    /**
     * List all zones.
     */
    public function listZones(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('/zones', [
            'page' => $page,
            'per_page' => $perPage,
            'order' => 'name',
        ]);

        if (!$response['success']) {
            return [];
        }

        return array_map(function ($zone) {
            return [
                'zone_id' => $zone['id'],
                'name' => $zone['name'],
                'status' => $zone['status'],
                'name_servers' => $zone['name_servers'] ?? [],
            ];
        }, $response['result'] ?? []);
    }

    private function get(string $endpoint, array $query = []): array
    {
        return $this->request('get', $endpoint, $query);
    }

    private function post(string $endpoint, array $data = []): array
    {
        return $this->request('post', $endpoint, $data);
    }

    private function patch(string $endpoint, array $data = []): array
    {
        return $this->request('patch', $endpoint, $data);
    }

    private function put(string $endpoint, array $data = []): array
    {
        return $this->request('put', $endpoint, $data);
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $client = Http::timeout(30)
                ->withHeaders([
                    'X-Auth-Email' => $this->email,
                    'X-Auth-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ]);

            if ($method === 'get') {
                $response = $client->get("{$this->baseUrl}{$endpoint}", $data);
            } else {
                $response = $client->{$method}("{$this->baseUrl}{$endpoint}", $data);
            }

            $result = $response->json() ?? [];

            Log::debug("Cloudflare API [{$method} {$endpoint}]", [
                'success' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error("Cloudflare API error [{$method} {$endpoint}]", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'errors' => [['message' => $e->getMessage()]],
            ];
        }
    }

    private function extractError(array $response): string
    {
        if (!empty($response['errors'])) {
            $messages = array_map(fn($e) => $e['message'] ?? 'Bilinmeyen hata', $response['errors']);
            return implode(', ', $messages);
        }

        return 'Bilinmeyen Cloudflare hatası.';
    }
}
