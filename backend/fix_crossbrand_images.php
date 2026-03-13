<?php

/**
 * Cross-brand image contamination fix script.
 *
 * Removes prensbet-branded images from non-prensbet sites.
 * Run via: php artisan tinker --execute="require 'fix_crossbrand_images.php';"
 */

use Illuminate\Support\Facades\DB;

$now = now();

// ──────────────────────────────────────────────────
// RiseBet görselleri (sunucuda yüklü dosyalar)
// ──────────────────────────────────────────────────
$riseDir = storage_path('app/public/uploads/promotions/risebet');
$riseFiles = glob($riseDir . '/*');
$riseImagePaths = array_values(array_map(
    fn($f) => '/storage/uploads/promotions/risebet/' . basename($f),
    $riseFiles
));

if (empty($riseImagePaths)) {
    echo "HATA: RiseBet görselleri bulunamadı: {$riseDir}\n";
    echo "Devam edilemiyor.\n";
    return;
}

echo "RiseBet görselleri: " . count($riseImagePaths) . " adet\n";

// Anasayfa için 3 görsel (büyük PNG'ler — Desktop/anasayfa/ kaynaklı)
$homepageImages = array_values(array_filter($riseImagePaths, function ($p) {
    $base = basename($p);
    return str_starts_with($base, '15838_') || str_starts_with($base, '1694_');
}));
if (count($homepageImages) < 3) {
    // Fallback: ilk 3 görseli kullan
    $homepageImages = array_slice($riseImagePaths, 0, 3);
}
echo "Anasayfa görselleri: " . count($homepageImages) . " adet\n\n";

// ──────────────────────────────────────────────────
// Helper: Tenant DB'ye bağlan
// ──────────────────────────────────────────────────
function switchTenant(string $db): void
{
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant');
    DB::reconnect('tenant');
}

// ──────────────────────────────────────────────────
// Helper: Prensbet featured_image → yeni görseller (round-robin)
// ──────────────────────────────────────────────────
function replacePrensImages(string $db, array $newImages, string $now): array
{
    switchTenant($db);

    $posts = DB::connection('tenant')->table('posts')
        ->where('featured_image', 'LIKE', '%prensbet%')
        ->orderBy('id')
        ->pluck('id');

    $updated = 0;
    foreach ($posts as $idx => $postId) {
        $img = $newImages[$idx % count($newImages)];
        DB::connection('tenant')->table('posts')
            ->where('id', $postId)
            ->update(['featured_image' => $img, 'updated_at' => $now]);
        $updated++;
    }

    return ['db' => $db, 'posts_updated' => $updated];
}

// ──────────────────────────────────────────────────
// Helper: Prensbet featured_image → NULL
// ──────────────────────────────────────────────────
function nullifyPrensImages(string $db, string $now): array
{
    switchTenant($db);

    $updated = DB::connection('tenant')->table('posts')
        ->where('featured_image', 'LIKE', '%prensbet%')
        ->update(['featured_image' => null, 'updated_at' => $now]);

    // Ayrıca promo- görselleri de NULL yap (bunlar da prensbet markalı)
    $updated2 = DB::connection('tenant')->table('posts')
        ->where('featured_image', 'LIKE', '%/promo-%')
        ->where('featured_image', 'LIKE', '%promo-prensvip%')
        ->orWhere('featured_image', 'LIKE', '%promo-kumbarapuan%')
        ->orWhere('featured_image', 'LIKE', '%promo-kriptoekhediyefs%')
        ->orWhere('featured_image', 'LIKE', '%promo-genelbonuskurallari%')
        ->update(['featured_image' => null, 'updated_at' => $now]);

    return ['db' => $db, 'posts_nullified' => $updated + $updated2];
}

// ──────────────────────────────────────────────────
// Helper: Anasayfa HTML'den prensbet img tag'lerini kaldır/değiştir
// ──────────────────────────────────────────────────
function fixHomepageImages(string $db, ?array $replacementImages, string $now): array
{
    switchTenant($db);

    $homepage = DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->first();

    if (!$homepage) {
        $homepage = DB::connection('tenant')->table('pages')
            ->where('slug', 'ana-sayfa')
            ->first();
    }

    if (!$homepage) {
        return ['db' => $db, 'homepage' => 'not_found'];
    }

    $content = $homepage->content;
    $originalContent = $content;

    if ($replacementImages) {
        // Strateji A: prensbet img src → risebet img src
        $imgIdx = 0;
        $content = preg_replace_callback(
            '/<img\s[^>]*src="[^"]*prensbet[^"]*"[^>]*>/i',
            function ($match) use ($replacementImages, &$imgIdx) {
                $newSrc = $replacementImages[$imgIdx % count($replacementImages)];
                $imgIdx++;
                // Alt attribute'u da güncelle
                $newTag = preg_replace('/src="[^"]*"/', 'src="' . $newSrc . '"', $match[0]);
                $newTag = preg_replace('/alt="[^"]*"/', 'alt="RiseBet Promosyon"', $newTag);
                return $newTag;
            },
            $content
        );
    } else {
        // Strateji B/C: prensbet img tag'lerini tamamen kaldır
        $content = preg_replace('/<img\s[^>]*src="[^"]*prensbet[^"]*"[^>]*>\s*/i', '', $content);
    }

    // Ayrıca promo- görselli img tag'lerini de temizle (prensbet markalı promo görselleri)
    if (!$replacementImages) {
        $content = preg_replace('/<img\s[^>]*src="[^"]*\/promo-(prensvip|kumbarapuan|kriptoekhediyefs|genelbonuskurallari)[^"]*"[^>]*>\s*/i', '', $content);
    }

    if ($content !== $originalContent) {
        DB::connection('tenant')->table('pages')
            ->where('id', $homepage->id)
            ->update(['content' => $content, 'updated_at' => $now]);
        return ['db' => $db, 'homepage' => 'fixed'];
    }

    return ['db' => $db, 'homepage' => 'no_change'];
}

// ══════════════════════════════════════════════════
// STRATEJİ A: RiseBet siteleri → prensbet → risebet
// ══════════════════════════════════════════════════
echo "═══ STRATEJİ A: RiseBet siteleri ═══\n";

$riseDBs = [
    'tenant_1',
    'tenant_risebett_me',
    'tenant_risebette_com',
    'tenant_risebets_click',
    'tenant_risespor_com',
];

foreach ($riseDBs as $db) {
    $result = replacePrensImages($db, $riseImagePaths, $now);
    echo "  [{$db}] {$result['posts_updated']} post güncellendi\n";

    $hpResult = fixHomepageImages($db, $homepageImages, $now);
    echo "  [{$db}] Anasayfa: {$hpResult['homepage']}\n";
}

// ══════════════════════════════════════════════════
// STRATEJİ B: Diğer markalar → prensbet → NULL
// ══════════════════════════════════════════════════
echo "\n═══ STRATEJİ B: Casival, İlkBahis, RayzBet, PragmaticLive ═══\n";

$nullDBs = [
    'tenant_casival_me',
    'tenant_ilkbahis_click',
    'tenant_ilkbahis_link',
    'tenant_ilkbahisgiri_net',
    'tenant_ilkbahisonline_com',
    'tenant_rayzbet_net',
    'tenant_pragmaticlive_click',
];

foreach ($nullDBs as $db) {
    $result = nullifyPrensImages($db, $now);
    echo "  [{$db}] {$result['posts_nullified']} post NULL yapıldı\n";

    $hpResult = fixHomepageImages($db, null, $now);
    echo "  [{$db}] Anasayfa: {$hpResult['homepage']}\n";
}

// ══════════════════════════════════════════════════
// STRATEJİ C: Locabet/KayaCasino → sadece prensbet → NULL
// ══════════════════════════════════════════════════
echo "\n═══ STRATEJİ C: Locabet, KayaCasino ═══\n";

$mixedDBs = [
    'tenant_locabetbonus_me',
    'tenant_locabetcasino_com',
    'tenant_locabetgiris_site',
    'tenant_locabeet_com',
    'tenant_kayacasino_top',
];

foreach ($mixedDBs as $db) {
    $result = nullifyPrensImages($db, $now);
    echo "  [{$db}] {$result['posts_nullified']} post NULL yapıldı (locabet/kaya görselleri korundu)\n";

    $hpResult = fixHomepageImages($db, null, $now);
    echo "  [{$db}] Anasayfa: {$hpResult['homepage']}\n";
}

// ══════════════════════════════════════════════════
// ATLANAN SİTELER
// ══════════════════════════════════════════════════
echo "\n═══ ATLANACAK SİTELER ═══\n";
echo "  Prensbet (7 site): Doğru marka, değişiklik yok\n";
echo "  Lizabet  (4 site): Kendi MOBILE görselleri var\n";

echo "\n✓ Tamamlandı.\n";
