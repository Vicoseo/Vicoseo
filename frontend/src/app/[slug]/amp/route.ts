import { NextRequest, NextResponse } from 'next/server';
import { getPage, getPost, getSiteConfig, getPages, getPosts, getPopularPosts, getCategories } from '@/lib/api';
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
          return NextResponse.redirect(`${siteUrl}/blog/${slug}/amp`, 301);
        }
      } catch {
        // Not found
      }
    }

    if (!content) {
      return new NextResponse('Not Found', { status: 404 });
    }

    // Fetch extra data in parallel for header, footer, and post sections
    const [pagesRes, postsRes, popularRes, catRes] = await Promise.all([
      getPages(host, 1, 10).catch(() => ({ data: [] })),
      getPosts(host, 1, 6).catch(() => ({ data: [] })),
      getPopularPosts(host, 6).catch(() => ({ data: [] })),
      getCategories(host).catch(() => ({ data: [] })),
    ]);

    const pages = 'data' in pagesRes ? pagesRes.data : [];
    const recentPosts = 'data' in postsRes ? postsRes.data : [];
    const popularPosts = Array.isArray(popularRes.data) ? popularRes.data : [];
    const categories = Array.isArray(catRes.data) ? catRes.data : [];

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
      pages,
      popularPosts,
      recentPosts,
      categories,
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
