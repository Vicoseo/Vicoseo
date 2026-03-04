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

  return response;
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico).*)'],
};
