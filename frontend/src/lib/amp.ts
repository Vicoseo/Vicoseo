import { SiteConfig } from '@/types';

/**
 * Convert regular HTML content to AMP-compatible HTML
 */
export function convertToAmpHtml(html: string): string {
  let amp = html;

  // Remove <script> tags
  amp = amp.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');

  // Remove <style> tags from content (will be in <style amp-custom>)
  amp = amp.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');

  // Convert <img> to <amp-img>
  amp = amp.replace(/<img\s+([^>]*?)\/?>/gi, (_match, attrs: string) => {
    const srcMatch = attrs.match(/src=["']([^"']+)["']/i);
    const widthMatch = attrs.match(/width=["']?(\d+)["']?/i);
    const heightMatch = attrs.match(/height=["']?(\d+)["']?/i);
    const altMatch = attrs.match(/alt=["']([^"']*)["']/i);

    const src = srcMatch ? srcMatch[1] : '';
    const width = widthMatch ? widthMatch[1] : '800';
    const height = heightMatch ? heightMatch[1] : '450';
    const alt = altMatch ? altMatch[1] : '';

    if (!src) return '';
    return `<amp-img src="${src}" width="${width}" height="${height}" alt="${alt}" layout="responsive"></amp-img>`;
  });

  // Remove <iframe> (not AMP-safe without amp-iframe component)
  amp = amp.replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '');

  // Remove <form> tags
  amp = amp.replace(/<\/?form[^>]*>/gi, '');

  // Remove <input>, <textarea>, <select>, <button> (form elements)
  amp = amp.replace(/<(input|textarea|select|button)\b[^>]*\/?>/gi, '');

  // Remove onclick and other JS event handlers from attributes
  amp = amp.replace(/\s+on\w+="[^"]*"/gi, '');
  amp = amp.replace(/\s+on\w+='[^']*'/gi, '');

  return amp;
}

/**
 * Build a complete AMP HTML document
 */
export function buildAmpDocument(opts: {
  title: string;
  description: string;
  canonicalUrl: string;
  content: string;
  site: SiteConfig;
  breadcrumbs?: { name: string; url: string }[];
  ogImage?: string;
}): string {
  const { title, description, canonicalUrl, content, site, breadcrumbs, ogImage } = opts;
  const siteUrl = `https://${site.domain}`;
  const image = ogImage || `${siteUrl}/storage/og-image.png`;
  const primaryColor = site.primary_color || '#007bff';
  const secondaryColor = site.secondary_color || '#6c757d';

  const schemaScripts: string[] = [];

  // WebPage schema
  schemaScripts.push(JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: title,
    description,
    url: canonicalUrl,
    isPartOf: { '@type': 'WebSite', name: site.name, url: siteUrl },
    inLanguage: 'tr',
  }));

  // BreadcrumbList schema
  if (breadcrumbs && breadcrumbs.length > 0) {
    schemaScripts.push(JSON.stringify({
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbs.map((b, i) => ({
        '@type': 'ListItem',
        position: i + 1,
        name: b.name,
        item: b.url,
      })),
    }));
  }

  const ampContent = convertToAmpHtml(content);

  return `<!doctype html>
<html amp lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1">
<link rel="canonical" href="${canonicalUrl}">
<title>${escapeHtml(title)}</title>
<meta name="description" content="${escapeHtml(description)}">
<meta property="og:title" content="${escapeHtml(title)}">
<meta property="og:description" content="${escapeHtml(description)}">
<meta property="og:url" content="${canonicalUrl}">
<meta property="og:type" content="website">
<meta property="og:locale" content="tr_TR">
<meta property="og:site_name" content="${escapeHtml(site.name)}">
<meta property="og:image" content="${image}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="${escapeHtml(title)}">
<meta name="twitter:description" content="${escapeHtml(description)}">
<meta name="twitter:image" content="${image}">
${site.favicon_url ? `<link rel="icon" href="${site.favicon_url.startsWith('http') ? site.favicon_url : siteUrl + site.favicon_url}">` : ''}
<script async src="https://cdn.ampproject.org/v0.js"></script>
<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;animation:none}</style></noscript>
${schemaScripts.map(s => `<script type="application/ld+json">${s}</script>`).join('\n')}
<style amp-custom>
*{box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;margin:0;padding:0;background:#0a0a1a;color:#e0e0e0;line-height:1.7;-webkit-font-smoothing:antialiased}
a{color:${primaryColor};text-decoration:none}
a:hover{text-decoration:underline}
.amp-header{background:#111;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #222}
.amp-header .logo{font-size:20px;font-weight:700;color:#fff;text-decoration:none}
.amp-header .login-btn{background:${primaryColor};color:#fff;padding:8px 20px;border-radius:6px;font-size:14px;font-weight:600;text-decoration:none}
.amp-breadcrumbs{max-width:800px;margin:16px auto 0;padding:0 20px;font-size:13px;color:#888}
.amp-breadcrumbs a{color:#aaa}
.amp-breadcrumbs span{margin:0 6px}
.amp-main{max-width:800px;margin:0 auto;padding:24px 20px 48px}
.amp-main h1{font-size:28px;line-height:1.3;margin:0 0 20px;color:#fff}
.amp-main h2{font-size:22px;line-height:1.3;margin:32px 0 14px;color:#fff;border-left:3px solid ${primaryColor};padding-left:12px}
.amp-main h3{font-size:18px;margin:24px 0 10px;color:#f0f0f0}
.amp-main p{margin:0 0 16px;color:#ccc;font-size:16px}
.amp-main strong{color:#e8e8e8}
.amp-main ul,.amp-main ol{margin:0 0 16px;padding-left:24px;color:#ccc}
.amp-main li{margin-bottom:6px}
.amp-main amp-img{border-radius:8px;margin:16px 0;overflow:hidden}
.amp-main article{padding:0}
.amp-main table{width:100%;border-collapse:collapse;margin:16px 0}
.amp-main th,.amp-main td{padding:10px 12px;border:1px solid #333;text-align:left;font-size:14px}
.amp-main th{background:#1a1a2e;color:#fff}
.amp-main td{color:#ccc}
.amp-cta{display:block;text-align:center;background:${primaryColor};color:#fff;padding:14px 24px;border-radius:8px;font-weight:700;font-size:16px;margin:32px 0;text-decoration:none}
.amp-cta:hover{opacity:0.9;text-decoration:none}
.amp-footer{background:#111;border-top:1px solid #222;padding:24px 20px;text-align:center;color:#666;font-size:13px}
.amp-footer a{color:#888;margin:0 8px}
</style>
</head>
<body>
<header class="amp-header">
<a href="${siteUrl}" class="logo">${escapeHtml(site.name)}</a>
${site.login_url || site.entry_url ? `<a href="/go/login" class="login-btn">Giriş Yap</a>` : ''}
</header>
${breadcrumbs && breadcrumbs.length > 0 ? `
<nav class="amp-breadcrumbs">
${breadcrumbs.map((b, i) => i < breadcrumbs.length - 1 ? `<a href="${b.url}">${escapeHtml(b.name)}</a><span>›</span>` : `<span>${escapeHtml(b.name)}</span>`).join('')}
</nav>` : ''}
<main class="amp-main">
${ampContent}
</main>
<footer class="amp-footer">
<p>&copy; ${new Date().getFullYear()} ${escapeHtml(site.name)}. Tüm hakları saklıdır.</p>
<p><a href="${siteUrl}">Ana Sayfa</a><a href="${siteUrl}/hakkimizda">Hakkımızda</a><a href="${siteUrl}/iletisim">İletişim</a></p>
</footer>
</body>
</html>`;
}

function escapeHtml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
