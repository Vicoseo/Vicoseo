import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { getCurrentDomain } from '@/lib/domain';
import { getPage, getSiteConfig } from '@/lib/api';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  try {
    const [res, siteRes] = await Promise.all([
      getPage(domain, slug),
      getSiteConfig(domain),
    ]);
    const page = res.data;
    const siteUrl = `https://${siteRes.data.domain}`;
    const title = page.meta_title || page.title;
    const description = page.meta_description || undefined;

    return {
      title,
      description,
      keywords: page.meta_keywords || undefined,
      alternates: {
        canonical: `${siteUrl}/${slug}`,
      },
      openGraph: {
        title,
        description,
        url: `${siteUrl}/${slug}`,
        type: 'website',
        locale: 'tr_TR',
        siteName: siteRes.data.name,
        images: [{ url: `${siteUrl}/storage/og-image.png`, width: 1200, height: 630, alt: siteRes.data.name }],
      },
      twitter: {
        card: 'summary_large_image',
        title,
        description,
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
      title: 'Page Not Found',
    };
  }
}

export default async function DynamicPage({ params }: PageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let page;
  let siteName = '';
  let siteUrl = '';

  try {
    const [res, siteRes] = await Promise.all([
      getPage(domain, slug),
      getSiteConfig(domain),
    ]);
    page = res.data;
    siteName = siteRes.data.name;
    siteUrl = `https://${siteRes.data.domain}`;
  } catch {
    notFound();
  }

  if (!page) {
    notFound();
  }

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
        name: page.title,
        item: `${siteUrl}/${slug}`,
      },
    ],
  };

  const webPageSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: page.meta_title || page.title,
    description: page.meta_description || '',
    url: `${siteUrl}/${slug}`,
    isPartOf: {
      '@type': 'WebSite',
      name: siteName,
      url: siteUrl,
    },
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
        dangerouslySetInnerHTML={{ __html: JSON.stringify(webPageSchema) }}
      />
      <div className="page-content">
        <h1>{page.title}</h1>
        <div dangerouslySetInnerHTML={{ __html: page.content }} />
      </div>
    </>
  );
}
