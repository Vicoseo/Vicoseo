import { NextResponse } from 'next/server';

export async function GET() {
  return new NextResponse('', {
    headers: {
      'Content-Type': 'text/plain',
      'Cache-Control': 'public, max-age=86400',
    },
  });
}
