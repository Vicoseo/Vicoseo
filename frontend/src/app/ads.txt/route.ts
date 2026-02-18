import { NextResponse } from 'next/server';

export async function GET() {
  const content = 'google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0\nplaceholder.example.com, placeholder, DIRECT';
  return new NextResponse(content, {
    headers: {
      'Content-Type': 'text/plain',
      'Cache-Control': 'public, max-age=86400',
    },
  });
}
