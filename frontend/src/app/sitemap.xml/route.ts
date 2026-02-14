import { NextResponse } from 'next/server';
import { getCurrentDomain } from '@/lib/domain';
import { getPages, getPosts } from '@/lib/api';

export const revalidate = 3600;

export async function GET() {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  const baseUrl = `https://${domain}`;

  // Fetch latest update times for each content type
  let pageLastMod = new Date().toISOString();
  let postLastMod = new Date().toISOString();
  let hasPages = false;
  let hasPosts = false;

  try {
    const pagesRes = await getPages(domain, 1);
    if (pagesRes.data && pagesRes.data.length > 0) {
      hasPages = true;
      // Use the most recent updated_at from pages
      const dates = pagesRes.data
        .map((p) => new Date(p.updated_at || p.created_at || Date.now()))
        .sort((a, b) => b.getTime() - a.getTime());
      if (dates.length > 0) pageLastMod = dates[0].toISOString();
    }
  } catch {
    // pages unavailable
  }

  try {
    const postsRes = await getPosts(domain, 1);
    if (postsRes.data && postsRes.data.length > 0) {
      hasPosts = true;
      const dates = postsRes.data
        .map((p) => new Date(p.published_at || p.updated_at || Date.now()))
        .sort((a, b) => b.getTime() - a.getTime());
      if (dates.length > 0) postLastMod = dates[0].toISOString();
    }
  } catch {
    // posts unavailable
  }

  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
  xml += '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';

  if (hasPages) {
    xml += '  <sitemap>\n';
    xml += `    <loc>${baseUrl}/page-sitemap.xml</loc>\n`;
    xml += `    <lastmod>${pageLastMod}</lastmod>\n`;
    xml += '  </sitemap>\n';
  }

  if (hasPosts) {
    xml += '  <sitemap>\n';
    xml += `    <loc>${baseUrl}/post-sitemap.xml</loc>\n`;
    xml += `    <lastmod>${postLastMod}</lastmod>\n`;
    xml += '  </sitemap>\n';
  }

  xml += '</sitemapindex>';

  return new NextResponse(xml, {
    status: 200,
    headers: {
      'Content-Type': 'application/xml; charset=UTF-8',
      'Cache-Control': 'public, max-age=3600, s-maxage=3600',
      'X-Robots-Tag': 'noindex',
    },
  });
}
