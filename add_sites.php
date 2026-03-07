<?php

require __DIR__ . "/backend/vendor/autoload.php";
$app = require __DIR__ . "/backend/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$tm = app(\App\Services\TenantManager::class);

$domains = [
    ["ilkbahis.click", "İlkBahis", "tenant_ilkbahis_click"],
    ["ilkbahis.link", "İlkBahis", "tenant_ilkbahis_link"],
    ["ilkbahisgiri.net", "İlkBahis Giriş", "tenant_ilkbahisgiri_net"],
    ["ilkbahisonline.com", "İlkBahis Online", "tenant_ilkbahisonline_com"],
];

foreach ($domains as $d) {
    $existing = \App\Models\Site::where("domain", $d[0])->first();
    if ($existing) {
        echo "SKIP: {$d[0]} zaten mevcut (ID: {$existing->id})\n";
        continue;
    }

    $site = \App\Models\Site::create([
        "domain" => $d[0],
        "name" => $d[1],
        "db_name" => $d[2],
        "is_active" => true,
        "primary_color" => "#007bff",
        "secondary_color" => "#6c757d",
        "show_sponsors" => true,
    ]);

    try {
        $tm->createTenantDatabase($site);
        echo "OK: {$d[0]} (ID: {$site->id}) - DB ve tablolar olusturuldu\n";
    } catch (Exception $e) {
        echo "ERROR: {$d[0]} - " . $e->getMessage() . "\n";
    }
}
