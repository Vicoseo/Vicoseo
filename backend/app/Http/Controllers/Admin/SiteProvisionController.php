<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\JsonResponse;

class SiteProvisionController extends Controller
{
    /**
     * Provision SSL certificate and Nginx config for a site.
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

        // Run the provision script
        $output = [];
        $exitCode = 0;
        exec(
            sprintf('sudo /usr/local/bin/provision-site.sh %s 2>&1', escapeshellarg($domain)),
            $output,
            $exitCode
        );

        $rawOutput = implode("\n", $output);

        // Try to parse JSON from last line
        $lastLine = end($output);
        $result = json_decode($lastLine ?: '', true);

        if ($result && isset($result['success'])) {
            return response()->json($result, $result['success'] ? 200 : 500);
        }

        return response()->json([
            'success' => $exitCode === 0,
            'message' => $exitCode === 0
                ? "{$domain} başarıyla yapılandırıldı."
                : "Yapılandırma başarısız oldu.",
            'output' => $rawOutput,
        ], $exitCode === 0 ? 200 : 500);
    }

    /**
     * Check if a site has been provisioned (SSL + Nginx).
     */
    public function status(int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $domain = preg_replace('/^www\./', '', $site->domain);
        $confName = str_replace('.', '-', $domain);

        $hasNginx = file_exists("/etc/nginx/sites-enabled/{$confName}");
        $hasSsl = is_dir("/etc/letsencrypt/live/{$domain}");

        return response()->json([
            'domain' => $domain,
            'nginx_configured' => $hasNginx,
            'ssl_active' => $hasSsl,
            'provisioned' => $hasNginx && $hasSsl,
        ]);
    }
}
