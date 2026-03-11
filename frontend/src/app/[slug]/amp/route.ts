import { NextRequest, NextResponse } from 'next/server';
import { getPage, getPost, getSiteConfig } from '@/lib/api';
import { buildAmpDocument } from '@/lib/amp';

export const revalidate = 300;

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ slug: string }> }
) {
  const { slug } = await params;
  const host = request.headers.get('host')?.split(':')[0] || '';

  try {
    const siteRes = await getSiteConfig(host);
    const site = siteRes.data;
    const siteUrl = `https://${site.domain}`;

    // Try page first
    let title = '';
    let description = '';
    let content = '';

    try {
      const pageRes = await getPage(host, slug);
      if (pageRes.data) {
        title = pageRes.data.meta_title || pageRes.data.title;
        description = pageRes.data.meta_description || '';
        content = pageRes.data.content;
      }
    } catch {
      // Not a page, try post
      try {
        const postRes = await getPost(host, slug);
        if (postRes.data) {
          // Redirect to /blog/slug/amp
          return NextResponse.redirect(`${siteUrl}/blog/${slug}/amp`, 301);
        }
      } catch {
        // Not found
      }
    }

    if (!content) {
      return new NextResponse('Not Found', { status: 404 });
    }

    const html = buildAmpDocument({
      title,
      description,
      canonicalUrl: `${siteUrl}/${slug}`,
      content,
      site,
      breadcrumbs: [
        { name: 'Ana Sayfa', url: siteUrl },
        { name: title, url: `${siteUrl}/${slug}` },
      ],
    });

    return new NextResponse(html, {
      headers: {
        'Content-Type': 'text/html; charset=utf-8',
        'Cache-Control': 'public, s-maxage=300, stale-while-revalidate=600',
      },
    });
  } catch {
    return new NextResponse('Not Found', { status: 404 });
  }
}
