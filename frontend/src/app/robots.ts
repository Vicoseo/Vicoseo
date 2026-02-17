import type { MetadataRoute } from 'next';
import { getCurrentDomain, } from '@/lib/domain';
import { getSiteConfig } from '@/lib/api';

export default async function robots(): Promise<MetadataRoute.Robots> {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  try {
    const siteRes = await getSiteConfig(domain);
    const site = siteRes.data;

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
      {
        userAgent: 'Googlebot',
        allow: '/',
      },
    ],
    sitemap: `https://${domain}/sitemap.xml`,
  };
}
