import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

// ─── Domain Redirect Cache (60s TTL) ───
const redirectCache = new Map<string, { target: string | null; expires: number }>();
const CACHE_TTL = 60_000;
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';

// ─── Slug Redirect Cache (5min TTL) ───
const slugRedirectCache = new Map<string, { map: Record<string, string> | null; expires: number }>();
const SLUG_CACHE_TTL = 300_000;

async function getSlugRedirects(domain: string): Promise<Record<string, string> | null> {
  const cached = slugRedirectCache.get(domain);
  if (cached && cached.expires > Date.now()) return cached.map;
  try {
    const res = await fetch(`${API_URL}/site/slug-redirects`, {
      headers: { 'X-Tenant-Domain': domain, Accept: 'application/json' },
      signal: AbortSignal.timeout(3000),
    });
    if (!res.ok) {
      slugRedirectCache.set(domain, { map: null, expires: Date.now() + SLUG_CACHE_TTL });
      return null;
    }
    const { data } = await res.json();
    slugRedirectCache.set(domain, { map: data || null, expires: Date.now() + SLUG_CACHE_TTL });
    return data || null;
  } catch {
    slugRedirectCache.set(domain, { map: null, expires: Date.now() + SLUG_CACHE_TTL });
    return null;
  }
}

async function getDomainRedirect(domain: string): Promise<string | null> {
  const cached = redirectCache.get(domain);
  if (cached && cached.expires > Date.now()) return cached.target;
  try {
    const res = await fetch(`${API_URL}/site/config`, {
      headers: { 'X-Tenant-Domain': domain, Accept: 'application/json' },
      signal: AbortSignal.timeout(3000),
    });
    if (!res.ok) throw new Error('API error');
    const { data } = await res.json();
    const target = data?.fallback_domain || null;
    redirectCache.set(domain, { target, expires: Date.now() + CACHE_TTL });
    return target;
  } catch {
    return null;
  }
}

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const host = request.headers.get('host')?.split(':')[0] || '';

  // ─── Yandex Verification File ───
  const yandexMatch = pathname.match(/^\/yandex_([a-f0-9]+)\.html$/);
  if (yandexMatch) {
    const code = yandexMatch[1];
    return new NextResponse(
      `<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>Doğrulama: ${code}</body></html>`,
      { status: 200, headers: { 'Content-Type': 'text/html; charset=UTF-8' } }
    );
  }

  // ─── Domain-level 301 Redirect ───
  const redirectTarget = await getDomainRedirect(host);
  if (redirectTarget && redirectTarget !== host) {
    const target = new URL(`https://${redirectTarget}${pathname}${request.nextUrl.search}`);
    return NextResponse.redirect(target, 301);
  }

  // Trailing slash redirect (308 permanent) to prevent duplicate content
  if (pathname !== '/' && pathname.endsWith('/')) {
    const url = request.nextUrl.clone();
    url.pathname = pathname.slice(0, -1);
    return NextResponse.redirect(url, 308);
  }

  // ─── ?page=1 canonical redirect ───
  if (request.nextUrl.searchParams.get('page') === '1') {
    const url = request.nextUrl.clone();
    url.searchParams.delete('page');
    return NextResponse.redirect(url, 301);
  }

  // ─── Old Slug → New Slug 301 Redirect ───
  const blogMatch = pathname.match(/^\/blog\/(.+)$/);
  if (blogMatch) {
    const oldSlug = blogMatch[1];
    const redirects = await getSlugRedirects(host);
    if (redirects && redirects[oldSlug]) {
      const url = request.nextUrl.clone();
      url.pathname = `/blog/${redirects[oldSlug]}`;
      return NextResponse.redirect(url, 301);
    }
  }

  const response = NextResponse.next();

  // Pass the host header through for server components
  response.headers.set('x-current-domain', host);

  // ─── BTK Bot Detection (Layer 3) ───
  const userAgent = request.headers.get('user-agent') || '';
  const sanitizeFromNginx = request.headers.get('x-sanitize-content');
  const cfBotScore = request.headers.get('cf-bot-score');

  // Whitelist legitimate search engine bots — never block these
  const isSearchBot = /googlebot|bingbot|yandex|baiduspider|duckduckbot|slurp/i.test(userAgent);

  // Detect suspicious visitors
  const isSuspicious =
    sanitizeFromNginx === 'true' ||
    (!userAgent || userAgent.length < 20) ||
    /python|curl|wget|scrapy|phantom|headless|puppeteer|selenium|httpclient|java\//i.test(userAgent) ||
    (cfBotScore !== null && parseInt(cfBotScore) < 10);

  if (isSuspicious && !isSearchBot) {
    response.headers.set('x-sanitize-content', 'true');
  }

  return response;
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico).*)'],
};
