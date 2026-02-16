import { NextRequest, NextResponse } from 'next/server';
import { getRedirect } from '@/lib/api';

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ slug: string }> }
) {
  const { slug } = await params;
  const host = request.headers.get('host') || 'localhost';
  const domain = host.split(':')[0];

  try {
    const response = await getRedirect(domain, slug);
    const statusCode = response.data.status || 302;
    return NextResponse.redirect(response.data.target_url, statusCode);
  } catch {
    return NextResponse.json({ error: 'Redirect not found' }, { status: 404 });
  }
}
