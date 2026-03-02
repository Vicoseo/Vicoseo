<?php
/**
 * Cloudflare CDN Cache Rules Setup Script
 *
 * Creates cache rules for all 18 sites:
 * 1. CDN subdomain: Cache Everything (30 days)
 * 2. Main domain static files: Cache Everything (7 days)
 * 3. Main domain API/Admin: Bypass Cache
 *
 * Uses Cloudflare Rulesets API v4.
 *
 * Usage: php setup_cdn_cache_rules.php
 */

$apiKey = 'ecef56399e8b7a0546bd2e270c08da05b0d62';
$email  = 'ardaggs@protonmail.com';

$domains = [
    'rise-bets.com',
    'casival.me',
    'ilkbahis.click',
    'ilkbahis.link',
    'ilkbahisgiri.net',
    'ilkbahisonline.com',
    'prensbet.me',
    'risebett.me',
    'rayzbet.net',
    'prensbetgiris.online',
    'prensbetgiris.site',
    'prensbetgirisonline.one',
    'prenssbet.com',
    'prenssbet.net',
    'risebette.com',
    'risebets.click',
    'pragmaticlive.click',
    'risespor.com',
];

function cfRequest(string $method, string $endpoint, ?array $data = null): array
{
    global $apiKey, $email;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Email: ' . $email,
        'X-Auth-Key: ' . $apiKey,
        'Content-Type: application/json',
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);
    if (!$result) {
        return ['success' => false, 'errors' => [['message' => "HTTP $httpCode - Invalid JSON response"]]];
    }

    return $result;
}

function getZoneId(string $domain): ?string
{
    $result = cfRequest('GET', '/zones?' . http_build_query(['name' => $domain]));

    if (!$result['success'] || empty($result['result'])) {
        return null;
    }

    return $result['result'][0]['id'];
}

function getExistingRuleset(string $zoneId): ?array
{
    $result = cfRequest('GET', "/zones/$zoneId/rulesets/phases/http_request_cache_settings/entrypoint");

    if ($result['success'] && !empty($result['result'])) {
        return $result['result'];
    }

    return null;
}

function createCacheRules(string $zoneId, string $domain): array
{
    $rules = [
        // Rule 1: CDN subdomain - Cache Everything, 30 days
        [
            'expression'  => '(http.host eq "cdn.' . $domain . '")',
            'description' => 'CDN subdomain: Cache Everything 30d - ' . $domain,
            'action'      => 'set_cache_settings',
            'action_parameters' => [
                'cache'       => true,
                'edge_ttl'    => [
                    'mode'    => 'override_origin',
                    'default' => 2592000, // 30 days
                ],
                'browser_ttl' => [
                    'mode'    => 'override_origin',
                    'default' => 2592000, // 30 days
                ],
            ],
        ],
        // Rule 2: Main domain static files - Cache Everything, 7 days
        [
            'expression'  => '(http.host eq "' . $domain . '" and (starts_with(http.request.uri.path, "/_next/static/") or starts_with(http.request.uri.path, "/storage/")))',
            'description' => 'Static files: Cache 7d - ' . $domain,
            'action'      => 'set_cache_settings',
            'action_parameters' => [
                'cache'    => true,
                'edge_ttl' => [
                    'mode'    => 'override_origin',
                    'default' => 604800, // 7 days
                ],
                'browser_ttl' => [
                    'mode'    => 'override_origin',
                    'default' => 604800, // 7 days
                ],
            ],
        ],
        // Rule 3: API/Admin - Bypass Cache
        [
            'expression'  => '(http.host eq "' . $domain . '" and (starts_with(http.request.uri.path, "/api/") or starts_with(http.request.uri.path, "/admin/")))',
            'description' => 'API/Admin: Bypass Cache - ' . $domain,
            'action'      => 'set_cache_settings',
            'action_parameters' => [
                'cache' => false,
            ],
        ],
    ];

    // Check for existing ruleset
    $existing = getExistingRuleset($zoneId);

    if ($existing) {
        // Check if rules for this domain already exist
        $existingRules = $existing['rules'] ?? [];
        $domainRulesExist = false;
        foreach ($existingRules as $rule) {
            if (strpos($rule['description'] ?? '', $domain) !== false) {
                $domainRulesExist = true;
                break;
            }
        }

        if ($domainRulesExist) {
            return ['success' => true, 'skipped' => true, 'message' => 'Rules already exist'];
        }

        // Append new rules to existing ruleset
        $allRules = array_merge($existingRules, $rules);
        return cfRequest('PUT', "/zones/$zoneId/rulesets/phases/http_request_cache_settings/entrypoint", [
            'rules' => $allRules,
        ]);
    } else {
        // Create new ruleset
        return cfRequest('PUT', "/zones/$zoneId/rulesets/phases/http_request_cache_settings/entrypoint", [
            'rules' => $rules,
        ]);
    }
}

// Main execution
echo "=== Cloudflare Cache Rules Setup ===\n\n";

$success = 0;
$skipped = 0;
$failed  = 0;

foreach ($domains as $domain) {
    echo "Processing: $domain ... ";

    $zoneId = getZoneId($domain);
    if (!$zoneId) {
        echo "FAILED (zone not found)\n";
        $failed++;
        continue;
    }

    $result = createCacheRules($zoneId, $domain);

    if (!empty($result['skipped'])) {
        echo "SKIPPED (rules already exist)\n";
        $skipped++;
    } elseif ($result['success']) {
        echo "OK (3 cache rules created)\n";
        $success++;
    } else {
        $error = $result['errors'][0]['message'] ?? json_encode($result['errors'] ?? $result);
        echo "FAILED ($error)\n";
        $failed++;
    }

    // Rate limit
    usleep(500000); // 500ms
}

echo "\n=== Summary ===\n";
echo "Configured: $success\n";
echo "Skipped:    $skipped\n";
echo "Failed:     $failed\n";
echo "Total:      " . count($domains) . "\n";
