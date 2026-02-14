<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NamesiloService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('domains.namesilo.api_key');
        $this->baseUrl = config('domains.namesilo.sandbox')
            ? 'https://sandbox.namesilo.com/api'
            : 'https://www.namesilo.com/api';
    }

    /**
     * Get Namesilo account balance.
     */
    public function getBalance(): float
    {
        $response = $this->request('getAccountBalance');

        return (float) ($response['reply']['balance'] ?? 0);
    }

    /**
     * Check domain availability for one or more domains.
     */
    public function checkAvailability(array $domains): array
    {
        $response = $this->request('checkRegisterAvailability', [
            'domains' => implode(',', $domains),
        ]);

        $results = [];
        $reply = $response['reply'] ?? [];

        // Available domains
        if (isset($reply['available'])) {
            $available = $reply['available'];
            // Namesilo returns single domain as object, multiple as array
            if (isset($available['domain'])) {
                $items = is_array($available['domain']) && !isset($available['domain']['domain'])
                    ? $available['domain']
                    : [$available['domain']];
                foreach ($items as $item) {
                    if (is_array($item)) {
                        $results[] = [
                            'domain' => $item['domain'],
                            'available' => true,
                            'price' => (float) ($item['price'] ?? 0),
                        ];
                    } else {
                        $results[] = [
                            'domain' => (string) $item,
                            'available' => true,
                            'price' => 0,
                        ];
                    }
                }
            }
        }

        // Unavailable domains
        if (isset($reply['unavailable'])) {
            $unavailable = $reply['unavailable'];
            if (isset($unavailable['domain'])) {
                $items = is_array($unavailable['domain']) && !isset($unavailable['domain']['domain'])
                    ? $unavailable['domain']
                    : [$unavailable['domain']];
                foreach ($items as $item) {
                    $domain = is_array($item) ? ($item['domain'] ?? $item) : (string) $item;
                    $results[] = [
                        'domain' => $domain,
                        'available' => false,
                        'price' => null,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Register (purchase) a domain.
     */
    public function registerDomain(string $domain, int $years = 1): array
    {
        $response = $this->request('registerDomain', [
            'domain' => $domain,
            'years' => $years,
            'private' => 1,
            'auto_renew' => 0,
        ]);

        $reply = $response['reply'] ?? [];
        $code = (int) ($reply['code'] ?? 999);

        if ($code === 300) {
            return [
                'success' => true,
                'domain' => $domain,
                'message' => $reply['detail'] ?? 'Domain başarıyla satın alındı.',
            ];
        }

        return [
            'success' => false,
            'domain' => $domain,
            'message' => $reply['detail'] ?? 'Domain satın alma başarısız.',
            'code' => $code,
        ];
    }

    /**
     * Update nameservers for a domain.
     */
    public function setNameservers(string $domain, array $ns): bool
    {
        $params = ['domain' => $domain];
        foreach ($ns as $i => $server) {
            $params['ns' . ($i + 1)] = $server;
        }

        $response = $this->request('changeNameServers', $params);
        $code = (int) ($response['reply']['code'] ?? 999);

        return $code === 300;
    }

    /**
     * List all domains in the account.
     */
    public function listDomains(): array
    {
        $response = $this->request('listDomains');
        $reply = $response['reply'] ?? [];

        if (!isset($reply['domains']['domain'])) {
            return [];
        }

        $domains = $reply['domains']['domain'];

        // Single domain returns as string, multiple as array
        if (is_string($domains)) {
            return [$domains];
        }

        return $domains;
    }

    /**
     * Make a request to Namesilo API.
     */
    private function request(string $operation, array $params = []): array
    {
        $query = array_merge([
            'version' => '1',
            'type' => 'json',
            'key' => $this->apiKey,
        ], $params);

        try {
            $response = Http::timeout(30)
                ->get("{$this->baseUrl}/{$operation}", $query);

            $data = $response->json();

            Log::debug("Namesilo API [{$operation}]", [
                'params' => array_diff_key($params, ['key' => true]),
                'response_code' => $data['reply']['code'] ?? null,
            ]);

            return $data ?? [];
        } catch (\Exception $e) {
            Log::error("Namesilo API error [{$operation}]", [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
