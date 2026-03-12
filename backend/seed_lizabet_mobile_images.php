<?php
/**
 * Add MOBILE.jpg images as featured_image to existing articles that don't have one.
 * 18 MOBILE images distributed across 4 Lizabet sites.
 *
 * Run: php artisan tinker --execute="require 'seed_lizabet_mobile_images.php';"
 */

use App\Models\Site;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$images = [
    '/storage/promotions/lizabet/50-hosgeldin-MOBILE.jpg',
    '/storage/promotions/lizabet/500tl-deneme-MOBILE.jpg',
    '/storage/promotions/lizabet/15-cevrimsiz-MOBILE.jpg',
    '/storage/promotions/lizabet/20-kombine-MOBILE.jpg',
    '/storage/promotions/lizabet/20-spor-kayip-MOBILE.jpg',
    '/storage/promotions/lizabet/5-haftalik-jest-MOBILE.jpg',
    '/storage/promotions/lizabet/1m-anlik-cekim-MOBILE.jpg',
    '/storage/promotions/lizabet/20-casino-discount-MOBILE.jpg',
    '/storage/promotions/lizabet/20-kripto-yatirim-MOBILE.jpg',
    '/storage/promotions/lizabet/25-gece-kusu-MOBILE.jpg',
    '/storage/promotions/lizabet/25-ortaklik-MOBILE.jpg',
    '/storage/promotions/lizabet/30-kripto-kayip-MOBILE.jpg',
    '/storage/promotions/lizabet/50-kripto-slot-MOBILE.jpg',
    '/storage/promotions/lizabet/ertesi-gun-freespin-MOBILE.jpg',
    '/storage/promotions/lizabet/liza-sans-carki-MOBILE.jpg',
    '/storage/promotions/lizabet/cifte-sans-MOBILE.jpg',
    '/storage/promotions/lizabet/paylas-kazan-MOBILE.jpg',
    '/storage/promotions/lizabet/sadakat-MOBILE.jpg',
];

$domains = [
    'lizabetcasino.com',
    'lizabetgiris.site',
    'lizabahis.site',
    'girislizabet.site',
];

$totalUpdated = 0;
$imgIndex = 0;

foreach ($domains as $domain) {
    $site = Site::where('domain', $domain)->firstOrFail();
    Config::set('database.connections.tenant.database', $site->db_name);
    DB::purge('tenant');
    DB::reconnect('tenant');

    // Get posts without featured_image
    $posts = DB::connection('tenant')
        ->table('posts')
        ->whereNull('featured_image')
        ->orWhere('featured_image', '')
        ->orderBy('id')
        ->get();

    echo "[$domain] Found {$posts->count()} posts without featured_image\n";

    foreach ($posts as $post) {
        $image = $images[$imgIndex % count($images)];
        DB::connection('tenant')
            ->table('posts')
            ->where('id', $post->id)
            ->update(['featured_image' => $image]);

        echo "  Updated: {$post->slug} → " . basename($image) . "\n";
        $imgIndex++;
        $totalUpdated++;
    }
}

echo "\n--- Total updated: {$totalUpdated} posts across " . count($domains) . " sites ---\n";
echo "Images cycled through: {$imgIndex} (from pool of " . count($images) . ")\n";
