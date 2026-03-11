import { SiteConfig, Page, Post, Category, SocialLinks } from '@/types';

/**
 * Convert regular HTML content to AMP-compatible HTML
 * @param html - Raw HTML content
 * @param siteUrl - Site base URL (e.g. https://domain.com) for converting relative paths
 * @param removeHeroFigure - If true, removes <figure class="post-hero-image"> to avoid duplicate
 */
export function convertToAmpHtml(
  html: string,
  siteUrl: string,
  removeHeroFigure = false
): string {
  let amp = html;

  // Remove <script> tags
  amp = amp.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');

  // Remove <style> tags from content (will be in <style amp-custom>)
  amp = amp.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');

  // Remove hero figure duplicate if requested (blog posts where featured_image is already shown)
  if (removeHeroFigure) {
    amp = amp.replace(/<figure\s+class=["']post-hero-image["'][^>]*>[\s\S]*?<\/figure>/gi, '');
  }

  // Convert relative src paths to absolute URLs
  amp = amp.replace(/src=["'](\/storage\/[^"']+)["']/gi, (_match, path: string) => {
    return `src="${siteUrl}${path}"`;
  });

  // Also convert relative srcset paths
  amp = amp.replace(/srcset=["'](\/storage\/[^"']+)["']/gi, (_match, path: string) => {
    return `srcset="${siteUrl}${path}"`;
  });

  // Convert <img> to <amp-img> (handles both self-closing and regular)
  amp = amp.replace(/<img\s+([^>]*?)\/?>/gi, (_match, attrs: string) => {
    const srcMatch = attrs.match(/src=["']([^"']+)["']/i);
    const widthMatch = attrs.match(/width=["']?(\d+)["']?/i);
    const heightMatch = attrs.match(/height=["']?(\d+)["']?/i);
    const altMatch = attrs.match(/alt=["']([^"']*)["']/i);
    const styleMatch = attrs.match(/style=["']([^"']*)["']/i);

    let src = srcMatch ? srcMatch[1] : '';
    const width = widthMatch ? widthMatch[1] : '800';
    const height = heightMatch ? heightMatch[1] : '450';
    const alt = altMatch ? altMatch[1] : '';
    const style = styleMatch ? ` style="${styleMatch[1]}"` : '';

    if (!src) return '';

    // Convert relative paths to absolute
    if (src.startsWith('/') && !src.startsWith('//')) {
      src = `${siteUrl}${src}`;
    }

    return `<amp-img src="${src}" width="${width}" height="${height}" alt="${alt}" layout="responsive"${style}></amp-img>`;
  });

  // Wrap tables in overflow container for mobile
  amp = amp.replace(/<table/gi, '<div class="amp-table-wrap"><table');
  amp = amp.replace(/<\/table>/gi, '</table></div>');

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

interface BuildAmpOpts {
  title: string;
  description: string;
  canonicalUrl: string;
  content: string;
  site: SiteConfig;
  breadcrumbs?: { name: string; url: string }[];
  ogImage?: string;
  pages?: Page[];
  popularPosts?: Post[];
  recentPosts?: Post[];
  categories?: Category[];
}

/**
 * Build a complete AMP HTML document with full header, footer, and optional post sections
 */
export function buildAmpDocument(opts: BuildAmpOpts): string {
  const {
    title, description, canonicalUrl, content, site, breadcrumbs, ogImage,
    pages, popularPosts, recentPosts, categories,
  } = opts;

  const siteUrl = `https://${site.domain}`;
  const image = ogImage || `${siteUrl}/storage/og-image.png`;
  const primaryColor = site.primary_color || '#007bff';
  const socialLinks = site.social_links || {};
  const logoUrl = site.logo_url
    ? (site.logo_url.startsWith('http') ? site.logo_url : `${siteUrl}${site.logo_url}`)
    : null;

  // Schema.org scripts
  const schemaScripts: string[] = [];

  schemaScripts.push(JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: title,
    description,
    url: canonicalUrl,
    isPartOf: { '@type': 'WebSite', name: site.name, url: siteUrl },
    inLanguage: 'tr',
  }));

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

  const ampContent = convertToAmpHtml(content, siteUrl);

  // Nav pages: top 3 pages + Blog link
  const navPages = (pages || []).slice(0, 3);

  // Build header HTML
  const headerHtml = buildHeader(site, siteUrl, logoUrl, navPages);

  // Build breadcrumbs HTML
  const breadcrumbsHtml = breadcrumbs && breadcrumbs.length > 0
    ? `<nav class="amp-breadcrumbs"><span>${breadcrumbs.map((b, i) =>
        i < breadcrumbs.length - 1
          ? `<a href="${b.url}">${escapeHtml(b.name)}</a><span class="sep">&rsaquo;</span>`
          : `<span class="current">${escapeHtml(b.name)}</span>`
      ).join('')}</span></nav>`
    : '';

  // Build featured posts section
  const featuredHtml = popularPosts && popularPosts.length > 0
    ? buildPostsSection('Popüler Yazılar', popularPosts.slice(0, 6), siteUrl, 'featured')
    : '';

  // Build recent posts section
  const recentHtml = recentPosts && recentPosts.length > 0
    ? buildPostsSection('Son Yazılar', recentPosts.slice(0, 6), siteUrl, 'recent')
    : '';

  // Build footer HTML
  const footerHtml = buildFooter(site, siteUrl, socialLinks, pages || [], recentPosts || [], categories || []);

  // Build CSS
  const css = buildAmpCss(primaryColor);

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
${css}
</style>
</head>
<body>
${headerHtml}
${breadcrumbsHtml}
<main class="amp-main">
${ampContent}
</main>
${featuredHtml}
${recentHtml}
${footerHtml}
</body>
</html>`;
}

function buildHeader(
  site: SiteConfig,
  siteUrl: string,
  logoUrl: string | null,
  navPages: Page[],
): string {
  const logoHtml = logoUrl
    ? `<a href="${siteUrl}" class="amp-header__logo-link"><amp-img src="${logoUrl}" width="120" height="34" alt="${escapeHtml(site.name)}" layout="fixed" class="amp-header__logo"></amp-img></a>`
    : `<a href="${siteUrl}" class="amp-header__logo-text">${escapeHtml(site.name)}</a>`;

  const navLinks = navPages.map(p =>
    `<a href="${siteUrl}/${p.slug}" class="amp-header__nav-link">${escapeHtml(p.title)}</a>`
  ).join('');

  const blogLink = `<a href="${siteUrl}/blog" class="amp-header__nav-link">Blog</a>`;

  const ctaHtml = site.login_url || site.entry_url
    ? `<a href="${siteUrl}/go/login" class="amp-header__cta">Giriş Yap</a>`
    : '';

  return `<header class="amp-header">
<div class="amp-header__inner">
${logoHtml}
<nav class="amp-header__nav">${navLinks}${blogLink}</nav>
${ctaHtml}
</div>
</header>`;
}

function buildPostsSection(
  sectionTitle: string,
  posts: Post[],
  siteUrl: string,
  type: 'featured' | 'recent',
): string {
  const sectionClass = type === 'featured' ? 'amp-featured' : 'amp-recent';
  const titleClass = type === 'featured' ? 'amp-featured__title' : 'amp-recent__title';

  const cardsHtml = posts.map(post => {
    const imgSrc = post.featured_image
      ? (post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image)
      : null;

    const imageHtml = imgSrc
      ? `<amp-img src="${imgSrc}" width="600" height="315" alt="${escapeHtml(post.title)}" layout="responsive" class="amp-post-card__img"></amp-img>`
      : '';

    const date = new Date(post.published_at).toLocaleDateString('tr-TR', {
      year: 'numeric', month: 'short', day: 'numeric',
    });

    const excerpt = post.excerpt
      ? `<p class="amp-post-card__excerpt">${escapeHtml(post.excerpt.substring(0, 120))}</p>`
      : '';

    const badge = type === 'featured' && post.category
      ? `<span class="amp-post-card__badge">${escapeHtml(post.category.name)}</span>`
      : '';

    return `<a href="${siteUrl}/blog/${post.slug}" class="amp-post-card">
${imageHtml}
<div class="amp-post-card__body">
${badge}
<h3 class="amp-post-card__title">${escapeHtml(post.title)}</h3>
${excerpt}
<span class="amp-post-card__date">${date}</span>
</div>
</a>`;
  }).join('\n');

  return `<section class="${sectionClass}">
<h2 class="${titleClass}">${sectionTitle}</h2>
<div class="amp-posts-grid">${cardsHtml}</div>
<div class="amp-posts-more"><a href="${siteUrl}/blog" class="amp-posts-more-link">Tüm Yazılar &rarr;</a></div>
</section>`;
}

function buildFooter(
  site: SiteConfig,
  siteUrl: string,
  socialLinks: SocialLinks,
  pages: Page[],
  recentPosts: Post[],
  categories: Category[],
): string {
  // Social links
  const socialMap: { key: keyof SocialLinks; label: string }[] = [
    { key: 'telegram', label: 'Telegram' },
    { key: 'instagram', label: 'Instagram' },
    { key: 'x', label: 'X' },
    { key: 'youtube', label: 'YouTube' },
    { key: 'tiktok', label: 'TikTok' },
    { key: 'whatsapp', label: 'WhatsApp' },
  ];

  const socialHtml = socialMap
    .filter(s => socialLinks[s.key])
    .map(s => `<a href="${socialLinks[s.key]}" class="amp-footer__social-link" rel="nofollow noopener" target="_blank">${s.label}</a>`)
    .join('');

  const socialSection = socialHtml
    ? `<div class="amp-footer__social">${socialHtml}</div>`
    : '';

  // Page nav links
  const pageLinksHtml = pages.slice(0, 6).map(p =>
    `<a href="${siteUrl}/${p.slug}" class="amp-footer__link">${escapeHtml(p.title)}</a>`
  ).join('');

  const pageNav = pageLinksHtml
    ? `<nav class="amp-footer__pages">${pageLinksHtml}</nav>`
    : '';

  // Recent posts links
  const postLinksHtml = recentPosts.slice(0, 8).map(p =>
    `<a href="${siteUrl}/blog/${p.slug}" class="amp-footer__link">${escapeHtml(p.title)}</a>`
  ).join('');

  const postsSection = postLinksHtml
    ? `<div class="amp-footer__posts"><span class="amp-footer__posts-title">Son Yazılar</span>${postLinksHtml}</div>`
    : '';

  // Categories
  const catLinksHtml = categories.slice(0, 8).map(c =>
    `<a href="${siteUrl}/kategori/${c.slug}" class="amp-footer__link">${escapeHtml(c.name)}</a>`
  ).join('');

  const catSection = catLinksHtml
    ? `<div class="amp-footer__cats"><span class="amp-footer__cats-title">Kategoriler</span>${catLinksHtml}</div>`
    : '';

  // Footer links from site config
  const footerConfigLinks = (site.footer_links || []).map(fl =>
    `<a href="${fl.link_url}" class="amp-footer__link" rel="nofollow">${escapeHtml(fl.link_text)}</a>`
  ).join('');

  const footerConfigNav = footerConfigLinks
    ? `<nav class="amp-footer__config-links">${footerConfigLinks}</nav>`
    : '';

  return `<footer class="amp-footer">
<div class="amp-footer__inner">
${socialSection}
${pageNav}
${postsSection}
${catSection}
${footerConfigNav}
<p class="amp-footer__copy">&copy; ${new Date().getFullYear()} ${escapeHtml(site.name)}. Tüm hakları saklıdır.</p>
</div>
</footer>`;
}

function buildAmpCss(primaryColor: string): string {
  return `*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;background:#0a0a1a;color:#e0e0e0;line-height:1.7;-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%}
a{color:${primaryColor};text-decoration:none}
a:hover{opacity:0.85}
h1,h2,h3,h4{line-height:1.3;margin-bottom:0.5em;word-break:break-word;color:#fff}

/* ===== HEADER ===== */
.amp-header{background:#0d0d20;border-bottom:1px solid rgba(255,255,255,0.08)}
.amp-header__inner{max-width:1200px;margin:0 auto;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.amp-header__logo-link{display:flex;align-items:center;flex-shrink:0}
.amp-header__logo{border-radius:0}
.amp-header__logo-text{font-size:20px;font-weight:700;color:#fff;text-decoration:none}
.amp-header__nav{display:flex;gap:18px;flex-wrap:wrap;align-items:center}
.amp-header__nav-link{color:#ccc;text-decoration:none;font-weight:500;font-size:14px;white-space:nowrap}
.amp-header__nav-link:hover{color:${primaryColor};opacity:1}
.amp-header__cta{display:inline-flex;align-items:center;padding:7px 16px;background:linear-gradient(135deg,#ffd700,#ff8c00);color:#0a0a1a;font-weight:700;font-size:12px;border-radius:6px;text-decoration:none;white-space:nowrap;text-transform:uppercase;letter-spacing:0.5px;flex-shrink:0}
.amp-header__cta:hover{opacity:0.9}

/* ===== BREADCRUMBS ===== */
.amp-breadcrumbs{max-width:800px;margin:16px auto 0;padding:0 20px;font-size:13px;color:#888}
.amp-breadcrumbs a{color:#aaa;text-decoration:none}
.amp-breadcrumbs a:hover{color:#fff}
.amp-breadcrumbs .sep{margin:0 6px;color:#555}
.amp-breadcrumbs .current{color:#ccc}

/* ===== MAIN CONTENT ===== */
.amp-main{max-width:800px;margin:0 auto;padding:24px 20px 48px}
.amp-main h1{font-size:28px;line-height:1.3;margin:0 0 20px;color:#fff}
.amp-main h2{font-size:22px;line-height:1.3;margin:32px 0 14px;color:#fff;border-left:3px solid ${primaryColor};padding-left:12px}
.amp-main h3{font-size:18px;margin:24px 0 10px;color:#f0f0f0}
.amp-main h4{font-size:16px;margin:20px 0 8px;color:#e8e8e8}
.amp-main p{margin:0 0 16px;color:#ccc;font-size:16px;line-height:1.8}
.amp-main strong{color:#e8e8e8}
.amp-main ul,.amp-main ol{margin:0 0 16px;padding-left:24px;color:#ccc}
.amp-main li{margin-bottom:6px;line-height:1.6}
.amp-main a{color:${primaryColor};text-decoration:underline}
.amp-main a:hover{opacity:0.8}
.amp-main amp-img{border-radius:8px;margin:16px 0;overflow:hidden}
.amp-main figure{margin:16px 0}
.amp-main figcaption{font-size:13px;color:#888;text-align:center;margin-top:8px}
.amp-main article{padding:0}

/* Blockquote */
.amp-main blockquote{border-left:4px solid ${primaryColor};padding:14px 18px;margin:20px 0;background:rgba(255,255,255,0.04);border-radius:0 6px 6px 0;color:#bbb;font-style:italic}
.amp-main blockquote p{margin-bottom:0;color:inherit}

/* Tables */
.amp-table-wrap{overflow-x:auto;margin:16px 0;-webkit-overflow-scrolling:touch}
.amp-main table{width:100%;border-collapse:collapse;min-width:480px}
.amp-main th,.amp-main td{padding:10px 12px;border:1px solid #2a2a4a;text-align:left;font-size:14px}
.amp-main th{background:#1a1a2e;color:#fff;font-weight:600}
.amp-main td{color:#ccc}
.amp-main tr:nth-child(even) td{background:rgba(255,255,255,0.02)}

/* CTA button in content */
.amp-cta{display:block;text-align:center;background:linear-gradient(135deg,#ffd700,#ff8c00);color:#0a0a1a;padding:14px 24px;border-radius:8px;font-weight:700;font-size:16px;margin:32px 0;text-decoration:none;text-transform:uppercase;letter-spacing:0.5px}
.amp-cta:hover{opacity:0.9;text-decoration:none}

/* ===== FEATURED / RECENT POSTS ===== */
.amp-featured,.amp-recent{max-width:1200px;margin:0 auto;padding:28px 24px}
.amp-featured__title,.amp-recent__title{font-size:20px;font-weight:700;margin-bottom:18px;text-align:center;text-shadow:0 1px 4px rgba(0,0,0,0.5)}
.amp-featured__title{color:#ffd700}
.amp-recent__title{color:#fff}
.amp-posts-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.amp-post-card{display:block;text-decoration:none;color:inherit;background:rgba(22,33,62,0.9);border:1px solid rgba(255,215,0,0.15);border-radius:8px;overflow:hidden}
.amp-post-card:hover{border-color:rgba(255,215,0,0.4)}
.amp-post-card__img{display:block}
.amp-post-card__body{padding:12px 14px 14px}
.amp-post-card__badge{display:inline-block;padding:2px 10px;background:linear-gradient(135deg,#ffd700,#ff8c00);color:#0a0a1a;font-size:11px;font-weight:700;border-radius:4px;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px}
.amp-post-card__title{font-size:14px;font-weight:600;line-height:1.4;color:#e0e0e0;margin-bottom:6px}
.amp-post-card:hover .amp-post-card__title{color:#ffd700}
.amp-post-card__excerpt{font-size:13px;color:#9aa5b4;line-height:1.5;margin-bottom:8px}
.amp-post-card__date{font-size:12px;color:#6b7a8d}
.amp-posts-more{text-align:center;margin-top:18px}
.amp-posts-more-link{display:inline-block;padding:10px 28px;border:2px solid #ffd700;color:#ffd700;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px}
.amp-posts-more-link:hover{background:#ffd700;color:#0a0a1a;opacity:1}

/* ===== FOOTER ===== */
.amp-footer{background:#1a1a2e;color:#ccc;padding:28px 20px;margin-top:auto;border-top:1px solid rgba(255,255,255,0.08)}
.amp-footer__inner{max-width:1200px;margin:0 auto;display:flex;flex-direction:column;align-items:center;gap:14px}
.amp-footer__social{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:center;width:100%;margin-bottom:6px}
.amp-footer__social-link{color:#ccc;font-size:13px;text-decoration:none;padding:4px 12px;border:1px solid rgba(255,255,255,0.15);border-radius:4px}
.amp-footer__social-link:hover{color:#fff;border-color:rgba(255,255,255,0.4);opacity:1}
.amp-footer__pages{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:center;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.1);width:100%}
.amp-footer__link{color:#aaa;font-size:13px;text-decoration:none}
.amp-footer__link:hover{color:#fff;opacity:1}
.amp-footer__posts,.amp-footer__cats{display:flex;flex-wrap:wrap;gap:6px 10px;align-items:center;justify-content:center;width:100%}
.amp-footer__posts-title,.amp-footer__cats-title{width:100%;text-align:center;font-size:13px;font-weight:600;color:#ddd;margin-bottom:4px}
.amp-footer__config-links{display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:center}
.amp-footer__copy{font-size:13px;color:#666;text-align:center}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
.amp-header__nav{display:none}
.amp-header__inner{padding:10px 16px}
.amp-main{padding:20px 16px 40px}
.amp-main h1{font-size:24px}
.amp-main h2{font-size:20px}
.amp-posts-grid{grid-template-columns:repeat(2,1fr);gap:12px}
.amp-featured,.amp-recent{padding:24px 16px}
.amp-footer{padding:24px 16px}
.amp-footer__pages{gap:8px}
}
@media(max-width:480px){
.amp-main{padding:16px 14px 32px}
.amp-main h1{font-size:22px}
.amp-main h2{font-size:18px}
.amp-main p{font-size:15px}
.amp-posts-grid{grid-template-columns:1fr;gap:10px}
.amp-featured,.amp-recent{padding:20px 14px}
.amp-featured__title,.amp-recent__title{font-size:17px;margin-bottom:12px}
.amp-footer{padding:20px 14px}
.amp-footer__link{font-size:12px}
.amp-footer__copy{font-size:12px}
.amp-footer__posts,.amp-footer__cats{gap:5px 8px}
}`;
}

function escapeHtml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
