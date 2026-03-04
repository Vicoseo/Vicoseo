import { NextResponse } from 'next/server';
import { getCurrentDomain } from '@/lib/domain';
import { getPosts } from '@/lib/api';
import { Post } from '@/types';

export const revalidate = 3600;

export async function GET() {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  const baseUrl = `https://${domain}`;

  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">\n';

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
      const images: { loc: string; title: string }[] = [];

      // Featured image
      if (post.featured_image) {
        const imgUrl = post.featured_image.startsWith('http')
          ? post.featured_image
          : `${baseUrl}${post.featured_image}`;
        images.push({ loc: imgUrl, title: post.title });
      }

      // Extract images from content
      if (post.content) {
        const imgRegex = /<img[^>]+src=["']([^"']+)["'][^>]*(?:alt=["']([^"']*)["'])?/gi;
        let match;
        while ((match = imgRegex.exec(post.content)) !== null) {
          const src = match[1];
          const alt = match[2] || post.title;
          const imgUrl = src.startsWith('http') ? src : `${baseUrl}${src}`;
          if (!images.some((i) => i.loc === imgUrl)) {
            images.push({ loc: imgUrl, title: alt });
          }
        }
      }

      if (images.length === 0) continue;

      xml += '  <url>\n';
      xml += `    <loc>${baseUrl}/blog/${escapeXml(post.slug)}</loc>\n`;

      for (const img of images) {
        xml += '    <image:image>\n';
        xml += `      <image:loc>${escapeXml(img.loc)}</image:loc>\n`;
        xml += `      <image:title>${escapeXml(img.title)}</image:title>\n`;
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
