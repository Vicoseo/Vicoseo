import { NextRequest, NextResponse } from 'next/server';
import { getPost, getSiteConfig, getPages, getPosts, getPopularPosts, getCategories } from '@/lib/api';
import { buildAmpDocument, convertToAmpHtml } from '@/lib/amp';

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

    // Fetch extra data in parallel
    const [pagesRes, postsRes, popularRes, catRes] = await Promise.all([
      getPages(host, 1, 10).catch(() => ({ data: [] })),
      getPosts(host, 1, 8).catch(() => ({ data: [] })),
      getPopularPosts(host, 6).catch(() => ({ data: [] })),
      getCategories(host).catch(() => ({ data: [] })),
    ]);

    const pages = 'data' in pagesRes ? pagesRes.data : [];
    const allPosts = 'data' in postsRes ? postsRes.data : [];
    const popularPosts = Array.isArray(popularRes.data) ? popularRes.data : [];
    const categories = Array.isArray(catRes.data) ? catRes.data : [];

    // Filter out current post from related/recent posts
    const recentPosts = allPosts.filter(p => p.slug !== slug).slice(0, 6);
    const filteredPopular = popularPosts.filter(p => p.slug !== slug).slice(0, 6);

    const title = post.meta_title || post.title;
    const description = post.meta_description || post.excerpt || '';

    // Build article content with featured image at top
    // Then convert HTML to AMP, removing hero figure duplicate
    let rawContent = post.content;

    // Add featured image at top as amp-img
    let featuredImgHtml = '';
    if (post.featured_image) {
      const imgSrc = post.featured_image.startsWith('/')
        ? `${siteUrl}${post.featured_image}`
        : post.featured_image;
      featuredImgHtml = `<amp-img src="${imgSrc}" width="800" height="450" alt="${escapeHtml(post.title)}" layout="responsive" class="amp-hero-img"></amp-img>`;
    }

    // Convert content to AMP, removing <figure class="post-hero-image"> to avoid duplicate
    const ampContent = convertToAmpHtml(rawContent, siteUrl, true);

    const formattedDate = new Date(post.published_at).toLocaleDateString('tr-TR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });

    const categoryBadge = post.category
      ? `<span class="amp-post-card__badge" style="margin-bottom:16px">${escapeHtml(post.category.name)}</span>`
      : '';

    const articleContent = `<article>
${featuredImgHtml}
${categoryBadge}
<h1>${escapeHtml(post.title)}</h1>
<p class="amp-post-date">${formattedDate}</p>
${ampContent}
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
      pages,
      popularPosts: filteredPopular,
      recentPosts,
      categories,
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

function escapeHtml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
