import type { MetadataRoute } from 'next';
import { getCurrentDomain } from '@/lib/domain';

export default async function robots(): Promise<MetadataRoute.Robots> {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
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
