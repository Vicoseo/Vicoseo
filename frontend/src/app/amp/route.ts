import { NextRequest, NextResponse } from 'next/server';
import { getPages, getPage, getSiteConfig, getPosts, getPopularPosts, getCategories } from '@/lib/api';
import { buildAmpDocument } from '@/lib/amp';

export const revalidate = 300;

export async function GET(request: NextRequest) {
  const host = request.headers.get('host')?.split(':')[0] || '';

  try {
    const siteRes = await getSiteConfig(host);
    const site = siteRes.data;
    const siteUrl = `https://${site.domain}`;

    // Get homepage content (first page by sort_order)
    const pagesRes = await getPages(host, 1, 10);
    const firstSlug = pagesRes.data?.[0]?.slug;
    if (!firstSlug) {
      return new NextResponse('Not Found', { status: 404 });
    }

    const pageRes = await getPage(host, firstSlug);
    if (!pageRes.data) {
      return new NextResponse('Not Found', { status: 404 });
    }

    const title = pageRes.data.meta_title || pageRes.data.title;
    const description = pageRes.data.meta_description || site.meta_description || '';

    // Build slider banners HTML for AMP (full-width, 3190×900)
    const sliders = site.promotions || [];
    let sliderHtml = '';
    if (sliders.length > 0) {
      const sliderItems = sliders.map((card: { image: string; title?: string | null; link_url?: string | null }) => {
        const imgSrc = card.image.startsWith('http') ? card.image : `${siteUrl}${card.image}`;
        const alt = card.title ? `${card.title}` : 'Promosyon';
        const titleAttr = card.title ? `${card.title} 2026` : 'Promosyon 2026';
        const href = card.link_url || null;

        const imgTag = `<amp-img src="${imgSrc}" width="3190" height="900" alt="${escapeHtml(alt)}" title="${escapeHtml(titleAttr)}" layout="responsive" class="amp-slider-img"></amp-img>`;

        if (href) {
          return `<a href="${href}" class="amp-slider-item" rel="noopener noreferrer nofollow" target="_blank">${imgTag}</a>`;
        }
        return `<div class="amp-slider-item">${imgTag}</div>`;
      }).join('\n');

      sliderHtml = `<section class="amp-slider-section">${sliderItems}</section>`;
    }

    // Build promotion cards HTML for AMP (square grid, 1080×1080)
    const cards = site.promotion_cards || [];
    let cardHtml = '';
    if (cards.length > 0) {
      const cardItems = cards.map((card: { image: string; title?: string | null; link_url?: string | null }) => {
        const imgSrc = card.image.startsWith('http') ? card.image : `${siteUrl}${card.image}`;
        const alt = card.title ? `${card.title}` : 'Promosyon';
        const titleAttr = card.title ? `${card.title} 2026` : 'Promosyon 2026';
        const href = card.link_url || null;

        const imgTag = `<amp-img src="${imgSrc}" width="1080" height="1080" alt="${escapeHtml(alt)}" title="${escapeHtml(titleAttr)}" layout="responsive" class="amp-promo-img"></amp-img>`;
        const caption = card.title ? `<p class="amp-promo-caption">${escapeHtml(card.title)}</p>` : '';

        if (href) {
          return `<a href="${href}" class="amp-promo-item" rel="noopener noreferrer nofollow" target="_blank">${imgTag}${caption}</a>`;
        }
        return `<div class="amp-promo-item">${imgTag}${caption}</div>`;
      }).join('\n');

      cardHtml = `<section class="amp-promo-grid">${cardItems}</section>`;
    }

    // Combine: sliders on top, then page content, then card grid
    const fullContent = sliderHtml + pageRes.data.content + cardHtml;

    // Fetch extra data for footer/post sections
    const [postsRes, popularRes, catRes] = await Promise.all([
      getPosts(host, 1, 6).catch(() => ({ data: [] })),
      getPopularPosts(host, 6).catch(() => ({ data: [] })),
      getCategories(host).catch(() => ({ data: [] })),
    ]);

    const pages = pagesRes.data || [];
    const recentPosts = 'data' in postsRes ? postsRes.data : [];
    const popularPosts = Array.isArray(popularRes.data) ? popularRes.data : [];
    const categories = Array.isArray(catRes.data) ? catRes.data : [];

    const html = buildAmpDocument({
      title,
      description,
      canonicalUrl: siteUrl,
      content: fullContent,
      site,
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

function escapeHtml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
