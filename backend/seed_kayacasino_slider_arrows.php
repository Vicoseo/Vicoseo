<?php
// Slider'a sol/sağ ok butonları ekle
// Çalıştır: php -r "require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); require 'seed_kayacasino_slider_arrows.php';"

$db = \Illuminate\Support\Facades\DB::connection('tenant');
$site = \App\Models\Site::where('domain', 'kayacasino.top')->first();
if (!$site) { echo "Site not found!\n"; exit; }

\Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $site->db_name);
$db->reconnect();

$page = $db->table('pages')->where('slug', 'anasayfa')->first();
$c = $page->content;

// Ok butonları HTML
$leftArrow = '<button onclick="var s=this.parentElement.querySelector(\'.promo-slider\');s.scrollBy({left:-s.offsetWidth,behavior:\'smooth\'})" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;border:1px solid rgba(255,255,255,0.2);background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);color:#fff;font-size:22px;cursor:pointer;z-index:3;display:flex;align-items:center;justify-content:center;transition:background 0.2s;line-height:1;" onmouseover="this.style.background=\'rgba(59,130,246,0.7)\'" onmouseout="this.style.background=\'rgba(0,0,0,0.55)\'">&#8249;</button>';

$rightArrow = '<button onclick="var s=this.parentElement.querySelector(\'.promo-slider\');s.scrollBy({left:s.offsetWidth,behavior:\'smooth\'})" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;border:1px solid rgba(255,255,255,0.2);background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);color:#fff;font-size:22px;cursor:pointer;z-index:3;display:flex;align-items:center;justify-content:center;transition:background 0.2s;line-height:1;" onmouseover="this.style.background=\'rgba(59,130,246,0.7)\'" onmouseout="this.style.background=\'rgba(0,0,0,0.55)\'">&#8250;</button>';

$arrows = "\n" . $leftArrow . "\n" . $rightArrow . "\n";

// Dot indicator'ın hemen öncesinde, relative container'ın içine ekle
// Pattern: ...promo-slider'ın closing div'i + relative container closing div + dots div
// Biz arrows'u relative container closing'den hemen önce ekliyoruz

$dotsDiv = '<div style="display:flex;justify-content:center;gap:8px;margin-top:14px;">';
$dotsPos = strpos($c, $dotsDiv);

if ($dotsPos === false) {
    echo "Dots section not found!\n";
    exit;
}

// dots'dan geriye doğru </div></div> bul (promo-slider close + relative container close)
$beforeDots = substr($c, 0, $dotsPos);
$afterDots = substr($c, $dotsPos);

// Son iki </div> -> relative container close'dan hemen önce arrows ekle
// beforeDots sonu: ...slide content...</div>\n</div>\n\n
$lastDivClose = strrpos($beforeDots, '</div>');
if ($lastDivClose !== false) {
    // Bu relative container'ın close'u, ondan hemen önce arrows koy
    $c = substr($beforeDots, 0, $lastDivClose) . $arrows . substr($beforeDots, $lastDivClose) . $afterDots;
    echo "Arrows eklendi!\n";
} else {
    echo "Container close not found!\n";
    exit;
}

$db->table('pages')->where('id', $page->id)->update([
    'content' => $c,
    'updated_at' => now(),
]);

echo "Content length: " . strlen($c) . " bytes\n";
