<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\CloudflareService;
use Illuminate\Http\JsonResponse;

class SiteProvisionController extends Controller
{
    public function __construct(
        private CloudflareService $cloudflare,
    ) {}

    /**
     * Check provision status: DNS resolution + Cloudflare status.
     * Wildcard Nginx handles all domains automatically — no per-site provisioning needed.
     */
    public function provision(int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $domain = $site->domain;

        if (empty($domain)) {
            return response()->json([
                'success' => false,
                'message' => 'Site domain alanı boş.',
            ], 422);
        }

        // Check DNS resolution
        $dnsResolved = !empty(dns_get_record($domain, DNS_A));

        // Check Cloudflare zone status
        $cfZone = $this->cloudflare->getZoneByName($domain);
        $cfActive = $cfZone && ($cfZone['status'] ?? '') === 'active';

        $provisioned = $dnsResolved && $cfActive;

        return response()->json([
            'success' => $provisioned,
            'message' => $provisioned
                ? "{$domain} aktif ve erişilebilir."
                : 'Site henüz tam yapılandırılmadı.',
            'dns_resolved' => $dnsResolved,
            'cloudflare_active' => $cfActive,
        ]);
    }

    /**
     * Check if a site is fully provisioned (DNS + Cloudflare).
     */
    public function status(int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $domain = preg_replace('/^www\./', '', $site->domain);

        $dnsResolved = !empty(dns_get_record($domain, DNS_A));
        $cfZone = $this->cloudflare->getZoneByName($domain);
        $cfActive = $cfZone && ($cfZone['status'] ?? '') === 'active';

        return response()->json([
            'domain' => $domain,
            'dns_resolved' => $dnsResolved,
            'cloudflare_active' => $cfActive,
            'provisioned' => $dnsResolved && $cfActive,
        ]);
    }
}
