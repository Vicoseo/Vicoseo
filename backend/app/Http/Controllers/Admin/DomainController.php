<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\CloudflareService;
use App\Services\NamesiloService;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DomainController extends Controller
{
    public function __construct(
        private NamesiloService $namesilo,
        private CloudflareService $cloudflare,
        private TenantManager $tenantManager,
    ) {}

    /**
     * Get Namesilo account balance.
     */
    public function balance(): JsonResponse
    {
        try {
            $balance = $this->namesilo->getBalance();

            return response()->json(['balance' => $balance]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Bakiye alınamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search domain availability across multiple TLDs.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $query = trim($request->input('q'));
        $baseName = preg_replace('/\.[a-z]+$/i', '', $query);

        // Check multiple TLDs
        $tlds = ['.com', '.net', '.click', '.link'];
        $domains = [];
        foreach ($tlds as $tld) {
            $domains[] = $baseName . $tld;
        }

        // If user entered a full domain, make sure it's first
        if (str_contains($query, '.')) {
            array_unshift($domains, $query);
            $domains = array_unique($domains);
        }

        try {
            $results = $this->namesilo->checkAvailability($domains);

            return response()->json(['data' => $results]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Arama başarısız: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Purchase a domain via Namesilo.
     */
    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'domain' => 'required|string',
            'years' => 'integer|min:1|max:10',
        ]);

        try {
            $result = $this->namesilo->registerDomain(
                $request->input('domain'),
                $request->integer('years', 1),
            );

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satın alma hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Full domain setup: CF zone + NS update + DNS records + SSL + CMS site + Nginx.
     */
    public function setup(Request $request): JsonResponse
    {
        $request->validate([
            'domain' => 'required|string',
            'site_name' => 'required|string|max:255',
            'server_ip' => 'nullable|ip',
        ]);

        $domain = $request->input('domain');
        $siteName = $request->input('site_name');
        $serverIp = $request->input('server_ip', config('domains.server_ip'));

        $steps = [];

        try {
            // Step 1: Add zone to Cloudflare
            $steps[] = ['step' => 'cloudflare_zone', 'status' => 'running'];

            $existing = $this->cloudflare->getZoneByName($domain);
            if ($existing) {
                $zoneId = $existing['zone_id'];
                $nameservers = $existing['name_servers'];
                $steps[0] = ['step' => 'cloudflare_zone', 'status' => 'done', 'message' => 'Zone zaten mevcut.'];
            } else {
                $cfResult = $this->cloudflare->addZone($domain);
                if (!$cfResult['success']) {
                    return response()->json([
                        'success' => false,
                        'steps' => array_merge($steps, [['step' => 'cloudflare_zone', 'status' => 'error', 'message' => $cfResult['message']]]),
                        'message' => 'Cloudflare zone eklenemedi: ' . $cfResult['message'],
                    ], 422);
                }
                $zoneId = $cfResult['zone_id'];
                $nameservers = $cfResult['name_servers'];
                $steps[0] = ['step' => 'cloudflare_zone', 'status' => 'done', 'zone_id' => $zoneId];
            }

            // Step 2: Update nameservers at Namesilo
            $steps[] = ['step' => 'nameservers', 'status' => 'running'];
            try {
                $nsResult = $this->namesilo->setNameservers($domain, $nameservers);
                $steps[1] = [
                    'step' => 'nameservers',
                    'status' => $nsResult ? 'done' : 'warning',
                    'message' => $nsResult ? 'NS güncellendi.' : 'NS güncellenemedi (domain farklı registrar\'da olabilir).',
                    'nameservers' => $nameservers,
                ];
            } catch (\Exception $e) {
                $steps[1] = [
                    'step' => 'nameservers',
                    'status' => 'warning',
                    'message' => 'NS güncellenemedi: ' . $e->getMessage(),
                    'nameservers' => $nameservers,
                ];
            }

            // Step 3: Add A records
            $steps[] = ['step' => 'dns_records', 'status' => 'running'];
            $aResult = $this->cloudflare->addDnsRecord($zoneId, 'A', $domain, $serverIp, true);
            $wwwResult = $this->cloudflare->addDnsRecord($zoneId, 'A', "www.{$domain}", $serverIp, true);
            $steps[2] = [
                'step' => 'dns_records',
                'status' => ($aResult['success'] || $wwwResult['success']) ? 'done' : 'error',
                'message' => 'A: ' . ($aResult['success'] ? 'OK' : ($aResult['message'] ?? 'hata'))
                    . ', www: ' . ($wwwResult['success'] ? 'OK' : ($wwwResult['message'] ?? 'hata')),
            ];

            // Step 4: Set SSL mode
            $steps[] = ['step' => 'ssl', 'status' => 'running'];
            $sslResult = $this->cloudflare->setSslMode($zoneId, 'full');
            $steps[3] = [
                'step' => 'ssl',
                'status' => $sslResult ? 'done' : 'warning',
                'message' => $sslResult ? 'SSL modu "full" olarak ayarlandı.' : 'SSL ayarlanamadı.',
            ];

            // Step 5: Create CMS site
            $steps[] = ['step' => 'cms_site', 'status' => 'running'];
            $existingSite = Site::where('domain', $domain)->first();
            if ($existingSite) {
                $site = $existingSite;
                $steps[4] = ['step' => 'cms_site', 'status' => 'done', 'message' => 'Site zaten mevcut.'];
            } else {
                $dbName = 'tenant_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($domain));
                $site = Site::create([
                    'domain' => $domain,
                    'name' => $siteName,
                    'db_name' => $dbName,
                    'is_active' => true,
                ]);
                $this->tenantManager->createTenantDatabase($site);
                $steps[4] = ['step' => 'cms_site', 'status' => 'done', 'site_id' => $site->id];
            }

            // Step 6: Nginx + SSL provision
            $steps[] = ['step' => 'nginx', 'status' => 'running'];
            $output = [];
            $exitCode = 0;
            exec(
                sprintf('sudo /usr/local/bin/provision-site.sh %s 2>&1', escapeshellarg($domain)),
                $output,
                $exitCode,
            );
            $rawOutput = implode("\n", $output);
            $steps[5] = [
                'step' => 'nginx',
                'status' => $exitCode === 0 ? 'done' : 'warning',
                'message' => $exitCode === 0 ? 'Nginx yapılandırıldı.' : 'Nginx yapılandırma uyarısı: ' . $rawOutput,
            ];

            return response()->json([
                'success' => true,
                'steps' => $steps,
                'message' => 'Kurulum tamamlandı.',
                'zone_id' => $zoneId,
                'nameservers' => $nameservers,
                'site_id' => $site->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Domain setup failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'steps' => $steps,
            ]);

            return response()->json([
                'success' => false,
                'steps' => $steps,
                'message' => 'Kurulum hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all Cloudflare zones.
     */
    public function cfZones(): JsonResponse
    {
        try {
            $zones = $this->cloudflare->listZones();

            return response()->json(['data' => $zones]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Zone listesi alınamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get zone detail with NS info.
     */
    public function cfZoneDetail(string $zoneId): JsonResponse
    {
        try {
            $zone = $this->cloudflare->getZone($zoneId);

            return response()->json($zone);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Zone detayı alınamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a DNS record to a Cloudflare zone.
     */
    public function cfAddDns(Request $request, string $zoneId): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:A,AAAA,CNAME,MX,TXT',
            'name' => 'required|string',
            'content' => 'required|string',
            'proxied' => 'boolean',
        ]);

        try {
            $result = $this->cloudflare->addDnsRecord(
                $zoneId,
                $request->input('type'),
                $request->input('name'),
                $request->input('content'),
                $request->boolean('proxied', true),
            );

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'DNS kaydı eklenemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trigger Cloudflare activation check for a pending zone.
     */
    public function retryActivation(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id' => 'required|string',
        ]);

        $zoneId = $request->input('zone_id');

        try {
            // First get the zone details
            $zone = $this->cloudflare->getZone($zoneId);
            if (!$zone['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone bulunamadı: ' . ($zone['message'] ?? 'Bilinmeyen hata'),
                ], 404);
            }

            $status = $zone['status'] ?? 'unknown';

            if ($status === 'active') {
                return response()->json([
                    'success' => true,
                    'status' => 'active',
                    'message' => 'Zone zaten aktif!',
                ]);
            }

            // Trigger activation check at Cloudflare
            $triggered = $this->cloudflare->triggerActivationCheck($zoneId);

            return response()->json([
                'success' => $triggered,
                'status' => $status,
                'message' => $triggered
                    ? 'Aktivasyon kontrolü tetiklendi. Cloudflare nameserver\'ları kontrol edecek. Birkaç dakika içinde tekrar kontrol edin.'
                    : 'Aktivasyon kontrolü tetiklenemedi. Nameserver ayarlarını kontrol edin.',
                'name_servers' => $zone['name_servers'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivasyon hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fix a pending zone: update NS at Namesilo + trigger CF activation.
     */
    public function fixPendingZone(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id' => 'required|string',
            'domain' => 'required|string',
        ]);

        $zoneId = $request->input('zone_id');
        $domain = $request->input('domain');
        $results = [];

        try {
            // Step 1: Get zone details from Cloudflare
            $zone = $this->cloudflare->getZone($zoneId);
            if (!$zone['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone bulunamadı.',
                ], 404);
            }

            $nameservers = $zone['name_servers'] ?? [];
            $currentStatus = $zone['status'] ?? 'unknown';

            if ($currentStatus === 'active') {
                return response()->json([
                    'success' => true,
                    'message' => 'Zone zaten aktif! İşlem gerekmez.',
                    'status' => 'active',
                    'results' => [],
                ]);
            }

            // Step 2: Try to update nameservers at Namesilo
            $nsUpdated = false;
            $nsMessage = '';
            if (!empty($nameservers)) {
                try {
                    $nsUpdated = $this->namesilo->setNameservers($domain, $nameservers);
                    $nsMessage = $nsUpdated
                        ? 'Nameserver\'lar Namesilo\'da güncellendi.'
                        : 'Namesilo NS güncellemesi başarısız (domain farklı registrar\'da olabilir).';
                } catch (\Exception $e) {
                    $nsMessage = 'Namesilo NS hatası: ' . $e->getMessage() . ' — Manuel NS güncelleme gerekebilir.';
                }
            }
            $results[] = [
                'step' => 'nameservers',
                'success' => $nsUpdated,
                'message' => $nsMessage,
                'nameservers' => $nameservers,
            ];

            // Step 3: Trigger Cloudflare activation check
            $cfTriggered = $this->cloudflare->triggerActivationCheck($zoneId);
            $results[] = [
                'step' => 'activation_check',
                'success' => $cfTriggered,
                'message' => $cfTriggered
                    ? 'Cloudflare aktivasyon kontrolü tetiklendi.'
                    : 'Aktivasyon kontrolü tetiklenemedi.',
            ];

            // Step 4: Verify DNS records exist
            $serverIp = config('domains.server_ip');
            if ($serverIp) {
                $aResult = $this->cloudflare->addDnsRecord($zoneId, 'A', $domain, $serverIp, true);
                $wwwResult = $this->cloudflare->addDnsRecord($zoneId, 'A', "www.{$domain}", $serverIp, true);
                $results[] = [
                    'step' => 'dns_records',
                    'success' => $aResult['success'] || $wwwResult['success'],
                    'message' => 'DNS A kayıtları: ' . ($aResult['success'] ? 'eklendi/mevcut' : ($aResult['message'] ?? 'hata'))
                        . ', www: ' . ($wwwResult['success'] ? 'eklendi/mevcut' : ($wwwResult['message'] ?? 'hata')),
                ];
            }

            $overallSuccess = $nsUpdated || $cfTriggered;
            $message = $nsUpdated
                ? 'NS güncellendi ve aktivasyon kontrolü tetiklendi. Birkaç dakika sonra zone aktif olmalı.'
                : 'NS güncellenemedi — nameserver\'ları manuel olarak güncelleyin: ' . implode(', ', $nameservers);

            return response()->json([
                'success' => $overallSuccess,
                'message' => $message,
                'status' => $currentStatus,
                'nameservers' => $nameservers,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Fix pending zone failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
                'results' => $results,
            ], 500);
        }
    }

    /**
     * Check overall domain status (NS, CF, DNS, SSL, Nginx).
     */
    public function domainStatus(string $domain): JsonResponse
    {
        $status = [
            'domain' => $domain,
            'cloudflare' => null,
            'cms_site' => null,
            'nginx' => false,
            'ssl' => false,
        ];

        // Check Cloudflare
        $zone = $this->cloudflare->getZoneByName($domain);
        if ($zone) {
            $status['cloudflare'] = [
                'zone_id' => $zone['zone_id'],
                'status' => $zone['status'],
                'name_servers' => $zone['name_servers'],
            ];
        }

        // Check CMS site
        $site = Site::where('domain', $domain)->first();
        if ($site) {
            $status['cms_site'] = [
                'id' => $site->id,
                'name' => $site->name,
                'is_active' => $site->is_active,
            ];
        }

        // Check Nginx & SSL
        $cleanDomain = preg_replace('/^www\./', '', $domain);
        $confName = str_replace('.', '-', $cleanDomain);
        $status['nginx'] = file_exists("/etc/nginx/sites-enabled/{$confName}");
        $status['ssl'] = is_dir("/etc/letsencrypt/live/{$cleanDomain}");

        return response()->json($status);
    }
}
