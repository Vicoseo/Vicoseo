import { NextRequest, NextResponse } from 'next/server';
import { getPost, getSiteConfig } from '@/lib/api';
import { buildAmpDocument } from '@/lib/amp';

export const revalidate = 60;

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ slug: string }> }
) {
  const { slug } = await params;
  const host = request.headers.get('host')?.split(':')[0] || '';

  try {
    const [postRes, siteRes] = await Promise.all([
      getPost(host, slug),
      getSiteConfig(host),
    ]);

    const post = postRes.data;
    const site = siteRes.data;
    const siteUrl = `https://${site.domain}`;

    if (!post) {
      return new NextResponse('Not Found', { status: 404 });
    }

    const title = post.meta_title || post.title;
    const description = post.meta_description || post.excerpt || '';

    // Add featured image at top of content if exists
    let content = post.content;
    if (post.featured_image) {
      const imgSrc = post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image;
      content = `<amp-img src="${imgSrc}" width="800" height="450" alt="${post.title.replace(/"/g, '&quot;')}" layout="responsive"></amp-img>\n${content}`;
    }

    const formattedDate = new Date(post.published_at).toLocaleDateString('tr-TR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });

    // Wrap content with article structure
    const articleContent = `<article>
<h1>${post.title}</h1>
<p style="color:#888;font-size:14px;margin-bottom:24px">${formattedDate}</p>
${content}
</article>`;

    const html = buildAmpDocument({
      title,
      description,
      canonicalUrl: `${siteUrl}/blog/${slug}`,
      content: articleContent,
      site,
      breadcrumbs: [
        { name: 'Ana Sayfa', url: siteUrl },
        { name: 'Blog', url: `${siteUrl}/blog` },
        { name: post.title, url: `${siteUrl}/blog/${slug}` },
      ],
      ogImage: post.featured_image
        ? (post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image)
        : undefined,
    });

    return new NextResponse(html, {
      headers: {
        'Content-Type': 'text/html; charset=utf-8',
        'Cache-Control': 'public, s-maxage=60, stale-while-revalidate=300',
      },
    });
  } catch {
    return new NextResponse('Not Found', { status: 404 });
  }
}
