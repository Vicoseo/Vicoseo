import type { MetadataRoute } from 'next';
import { getCurrentDomain, } from '@/lib/domain';
import { getSiteConfig } from '@/lib/api';

export default async function robots(): Promise<MetadataRoute.Robots | string> {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  // Check if the site has custom robots.txt
  try {
    const siteRes = await getSiteConfig(domain);
    const site = siteRes.data;

    if (site?.robots_txt) {
      // Return raw text - Next.js will serve it as-is
      return site.robots_txt as unknown as MetadataRoute.Robots;
    }

    if (site?.noindex_mode) {
      return {
        rules: [
          {
            userAgent: '*',
            disallow: '/',
          },
        ],
      };
    }
  } catch {
    // fallback to default
  }

  return {
    rules: [
      {
        userAgent: '*',
        allow: '/',
        disallow: ['/admin/', '/api/', '/go/'],
      },
    ],
    sitemap: `https://${domain}/sitemap.xml`,
  };
}
