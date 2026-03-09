import type { Metadata } from 'next';
import { notFound, permanentRedirect } from 'next/navigation';
import { getCurrentDomain } from '@/lib/domain';
import { getPage, getPost, getSiteConfig } from '@/lib/api';
import { Page, Post } from '@/types';
import ContactForm from '@/components/ContactForm';
import PromotionSlider from '@/components/PromotionSlider';

interface PageProps {
  params: Promise<{ slug: string }>;
}

type ContentResult =
  | { type: 'page'; data: Page }
  | { type: 'post'; data: Post };

async function findContent(domain: string, slug: string): Promise<ContentResult | null> {
  // Try page first
  try {
    const res = await getPage(domain, slug);
    if (res.data) return { type: 'page', data: res.data };
  } catch {
    // not a page
  }
  // Try post
  try {
    const res = await getPost(domain, slug);
    if (res.data) return { type: 'post', data: res.data };
  } catch {
    // not a post
  }
  return null;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  try {
    const [content, siteRes] = await Promise.all([
      findContent(domain, slug),
      getSiteConfig(domain),
    ]);

    if (!content) return { title: 'Sayfa Bulunamadı' };

    // Posts will be redirected to /blog/{slug}, no metadata needed
    if (content.type === 'post') return { title: content.data.title };

    const item = content.data;
    const siteUrl = `https://${siteRes.data.domain}`;
    const title = item.meta_title || item.title;
    const description = item.meta_description || undefined;

    return {
      title,
      description: description || undefined,
      alternates: {
        canonical: `${siteUrl}/${slug}`,
      },
      openGraph: {
        title,
        description: description || undefined,
        url: `${siteUrl}/${slug}`,
        type: 'website',
        locale: 'tr_TR',
        siteName: siteRes.data.name,
        images: [{ url: `${siteUrl}/storage/og-image.png`, width: 1200, height: 630, alt: siteRes.data.name }],
      },
      twitter: {
        card: 'summary_large_image',
        title,
        description: description || undefined,
        images: [`${siteUrl}/storage/og-image.png`],
      },
      robots: {
        index: true,
        follow: true,
        googleBot: {
          index: true,
          follow: true,
          'max-video-preview': -1,
          'max-image-preview': 'large',
          'max-snippet': -1,
        },
      },
    };
  } catch {
    return {
      title: 'Sayfa Bulunamadı',
    };
  }
}

export default async function DynamicPage({ params }: PageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let content: ContentResult | null = null;
  let siteName = '';
  let siteUrl = '';
  let site: import('@/types').SiteConfig | null = null;

  try {
    const siteRes = await getSiteConfig(domain);
    site = siteRes.data;
    siteName = site.name;
    siteUrl = `https://${site.domain}`;
    content = await findContent(domain, slug);
  } catch {
    notFound();
  }

  if (!content) {
    notFound();
  }

  // Posts should live at /blog/{slug} — 301 redirect
  if (content.type === 'post') {
    permanentRedirect(`/blog/${slug}`);
  }

  const item = content.data;

  const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      {
        '@type': 'ListItem',
        position: 1,
        name: 'Ana Sayfa',
        item: siteUrl,
      },
      {
        '@type': 'ListItem',
        position: 2,
        name: item.title,
        item: `${siteUrl}/${slug}`,
      },
    ],
  };

  const pageSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: item.meta_title || item.title,
    description: item.meta_description || '',
    url: `${siteUrl}/${slug}`,
    isPartOf: { '@type': 'WebSite', name: siteName, url: siteUrl },
    inLanguage: 'tr',
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(pageSchema) }}
      />
      {site && site.promotions && site.promotions.length > 0 && (
        <div style={{ maxWidth: 1200, margin: '12px auto', padding: '0 16px' }}>
          <PromotionSlider
            promotions={site.promotions}
            domain={site.domain}
            loginUrl={site.login_url || site.entry_url || undefined}
          />
        </div>
      )}
      <div className="page-content">
        <h1>{item.title}</h1>
        <div dangerouslySetInnerHTML={{ __html: item.content.replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"') }} />
        {slug === 'iletisim' && <ContactForm domain={domain} />}
      </div>
    </>
  );
}
