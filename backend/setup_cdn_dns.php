<?php
/**
 * Cloudflare CDN DNS Setup Script
 *
 * Creates cdn.DOMAIN CNAME records (proxied) for all 18 sites.
 * Uses Cloudflare API v4.
 *
 * Usage: php setup_cdn_dns.php
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

function checkExistingRecord(string $zoneId, string $name): bool
{
    $result = cfRequest('GET', "/zones/$zoneId/dns_records?" . http_build_query([
        'type' => 'CNAME',
        'name' => $name,
    ]));

    return $result['success'] && !empty($result['result']);
}

function createCdnCname(string $zoneId, string $domain): array
{
    $cdnName = 'cdn.' . $domain;

    // Check if record already exists
    if (checkExistingRecord($zoneId, $cdnName)) {
        return ['success' => true, 'skipped' => true, 'message' => 'Record already exists'];
    }

    return cfRequest('POST', "/zones/$zoneId/dns_records", [
        'type'    => 'CNAME',
        'name'    => 'cdn',
        'content' => $domain,
        'ttl'     => 1, // Auto
        'proxied' => true,
    ]);
}

// Main execution
echo "=== Cloudflare CDN DNS Setup ===\n\n";

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

    $result = createCdnCname($zoneId, $domain);

    if (!empty($result['skipped'])) {
        echo "SKIPPED (cdn.$domain already exists)\n";
        $skipped++;
    } elseif ($result['success']) {
        echo "OK (cdn.$domain created, proxied)\n";
        $success++;
    } else {
        $error = $result['errors'][0]['message'] ?? 'Unknown error';
        echo "FAILED ($error)\n";
        $failed++;
    }

    // Rate limit: small delay between requests
    usleep(300000); // 300ms
}

echo "\n=== Summary ===\n";
echo "Created: $success\n";
echo "Skipped: $skipped\n";
echo "Failed:  $failed\n";
echo "Total:   " . count($domains) . "\n";
