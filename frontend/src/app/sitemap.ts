import type { MetadataRoute } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getPages, getPosts } from '@/lib/api';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  let domain = 'localhost';
  try {
    domain = await getCurrentDomain();
  } catch {
    // fallback
  }

  const siteUrl = `https://${domain}`;
  const now = new Date().toISOString();

  const entries: MetadataRoute.Sitemap = [
    {
      url: siteUrl,
      lastModified: now,
      changeFrequency: 'daily',
      priority: 1.0,
    },
    {
      url: `${siteUrl}/blog`,
      lastModified: now,
      changeFrequency: 'daily',
      priority: 0.8,
    },
  ];

  try {
    const pagesRes = await getPages(domain, 1);
    for (const page of pagesRes.data || []) {
      if (page.slug === 'anasayfa') continue;
      entries.push({
        url: `${siteUrl}/${page.slug}`,
        lastModified: now,
        changeFrequency: 'weekly',
        priority: 0.7,
      });
    }
  } catch {
    // pages unavailable
  }

  try {
    const postsRes = await getPosts(domain, 1);
    for (const post of postsRes.data || []) {
      entries.push({
        url: `${siteUrl}/blog/${post.slug}`,
        lastModified: post.published_at,
        changeFrequency: 'monthly',
        priority: 0.6,
      });
    }
  } catch {
    // posts unavailable
  }

  return entries;
}
