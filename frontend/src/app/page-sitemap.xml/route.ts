import { NextResponse } from 'next/server';
import { getCurrentDomain } from '@/lib/domain';
import { getPages } from '@/lib/api';
import { Page } from '@/types';

export const revalidate = 3600;

export async function GET() {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  const baseUrl = `https://${domain}`;
  const now = new Date().toISOString();

  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';

  // Homepage — always present, highest priority
  xml += '  <url>\n';
  xml += `    <loc>${baseUrl}/</loc>\n`;
  xml += `    <lastmod>${now}</lastmod>\n`;
  xml += '    <changefreq>daily</changefreq>\n';
  xml += '    <priority>1.0</priority>\n';
  xml += '  </url>\n';

  try {
    // Fetch all pages with pagination
    const allPages: Page[] = [];
    let page = 1;
    let lastPage = 1;

    do {
      const pagesRes = await getPages(domain, page, 100);
      if (pagesRes.data) {
        allPages.push(...pagesRes.data);
      }
      lastPage = pagesRes.last_page || 1;
      page++;
    } while (page <= lastPage);

    for (const p of allPages) {
      if (['anasayfa', 'ana-sayfa'].includes(p.slug)) continue;

      const lastmod = p.updated_at || p.created_at || now;

      xml += '  <url>\n';
      xml += `    <loc>${baseUrl}/${escapeXml(p.slug)}</loc>\n`;
      xml += `    <lastmod>${new Date(lastmod).toISOString()}</lastmod>\n`;
      xml += '    <changefreq>weekly</changefreq>\n';
      xml += '    <priority>0.8</priority>\n';
      xml += '  </url>\n';
    }
  } catch {
    // pages unavailable
  }

  xml += '</urlset>';

  return new NextResponse(xml, {
    status: 200,
    headers: {
      'Content-Type': 'application/xml; charset=UTF-8',
    },
  });
}

function escapeXml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}
