import { NextResponse } from 'next/server';
import { getCurrentDomain } from '@/lib/domain';
import { getPosts } from '@/lib/api';

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
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">\n';

  // Blog index page
  xml += '  <url>\n';
  xml += `    <loc>${baseUrl}/blog</loc>\n`;
  xml += `    <lastmod>${now}</lastmod>\n`;
  xml += '    <changefreq>daily</changefreq>\n';
  xml += '    <priority>0.9</priority>\n';
  xml += '  </url>\n';

  try {
    // Fetch all posts (high per_page to get them all)
    const postsRes = await getPosts(domain, 1, 100);
    const posts = postsRes.data || [];

    for (const post of posts) {
      const lastmod = post.updated_at || post.published_at || now;

      xml += '  <url>\n';
      xml += `    <loc>${baseUrl}/blog/${post.slug}</loc>\n`;
      xml += `    <lastmod>${new Date(lastmod).toISOString()}</lastmod>\n`;
      xml += '    <changefreq>weekly</changefreq>\n';
      xml += '    <priority>0.8</priority>\n';
      if (post.featured_image) {
        const imgUrl = post.featured_image.startsWith('http') ? post.featured_image : `${baseUrl}${post.featured_image}`;
        xml += '    <image:image>\n';
        xml += `      <image:loc>${imgUrl}</image:loc>\n`;
        xml += `      <image:title>${post.title.replace(/&/g, '&amp;').replace(/</g, '&lt;')}</image:title>\n`;
        xml += '    </image:image>\n';
      }
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
      'Cache-Control': 'public, max-age=3600, s-maxage=3600',
    },
  });
}
