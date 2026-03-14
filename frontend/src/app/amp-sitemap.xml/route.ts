import { NextResponse } from 'next/server';
import { getCurrentDomain } from '@/lib/domain';
import { getPosts } from '@/lib/api';
import { Post } from '@/types';

export const dynamic = 'force-dynamic';

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

  // AMP homepage
  xml += '  <url>\n';
  xml += `    <loc>${baseUrl}/amp</loc>\n`;
  xml += `    <lastmod>${now}</lastmod>\n`;
  xml += '    <changefreq>daily</changefreq>\n';
  xml += '    <priority>0.8</priority>\n';
  xml += '  </url>\n';

  try {
    const allPosts: Post[] = [];
    let page = 1;
    let lastPage = 1;

    do {
      const postsRes = await getPosts(domain, page, 100);
      if (postsRes.data) {
        allPosts.push(...postsRes.data);
      }
      lastPage = postsRes.last_page || 1;
      page++;
    } while (page <= lastPage);

    for (const post of allPosts) {
      const lastmod = post.updated_at || post.published_at || now;

      xml += '  <url>\n';
      xml += `    <loc>${baseUrl}/blog/${escapeXml(post.slug)}/amp</loc>\n`;
      xml += `    <lastmod>${new Date(lastmod).toISOString()}</lastmod>\n`;
      xml += '    <changefreq>weekly</changefreq>\n';
      xml += '    <priority>0.7</priority>\n';
      xml += '  </url>\n';
    }
  } catch {
    // posts unavailable
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
