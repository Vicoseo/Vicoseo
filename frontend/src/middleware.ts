import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

// ─── Domain Redirect Cache (60s TTL) ───
const redirectCache = new Map<string, { target: string | null; expires: number }>();
const CACHE_TTL = 60_000;
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';

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
