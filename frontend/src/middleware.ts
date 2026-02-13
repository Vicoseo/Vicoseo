import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  const response = NextResponse.next();

  // Pass the host header through for server components
  const host = request.headers.get('host') || 'localhost';
  response.headers.set('x-current-domain', host.split(':')[0]);

  return response;
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico).*)'],
};
