import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  // Trailing slash redirect (308 permanent) to prevent duplicate content
  const { pathname } = request.nextUrl;
  if (pathname !== '/' && pathname.endsWith('/')) {
    const url = request.nextUrl.clone();
    url.pathname = pathname.slice(0, -1);
    return NextResponse.redirect(url, 308);
  }

  const response = NextResponse.next();

  // Pass the host header through for server components
  const host = request.headers.get('host') || 'localhost';
  response.headers.set('x-current-domain', host.split(':')[0]);

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
