import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getPages, getPage, getSiteConfig } from '@/lib/api';

export async function generateMetadata(): Promise<Metadata> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, pagesRes] = await Promise.all([
      getSiteConfig(domain),
      getPages(domain, 1),
    ]);

    const firstPage = pagesRes.data?.[0];
    const siteUrl = `https://${siteRes.data.domain}`;
    const title = firstPage?.meta_title || siteRes.data.meta_title || siteRes.data.name;
    const description =
      firstPage?.meta_description ||
      siteRes.data.meta_description ||
      `${siteRes.data.name} - Online bahis ve casino platformu`;

    return {
      title,
      description,
      keywords: firstPage?.meta_keywords || undefined,
      alternates: {
        canonical: siteUrl,
      },
      openGraph: {
        title,
        description,
        url: siteUrl,
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
      title: 'Home',
    };
  }
}

function extractFAQSchema(html: string) {
  const faqRegex = /<strong>(.*?)<\/strong>.*?(?:<br\s*\/?>|<\/p>\s*<p>)([\s\S]*?)(?=<strong>|<\/ul>|<\/div>|$)/gi;
  const faqs: { question: string; answer: string }[] = [];
  let match;
  while ((match = faqRegex.exec(html)) !== null) {
    const question = match[1].replace(/<[^>]*>/g, '').trim();
    const answer = match[2].replace(/<[^>]*>/g, '').trim();
    if (question && answer && question.includes('?')) {
      faqs.push({ question, answer });
    }
  }
  return faqs;
}

export default async function HomePage() {
  const domain = await getCurrentDomain();

  let firstPage = null;
  let siteName = 'our site';
  let siteUrl = '';

  try {
    const [pagesRes, siteRes] = await Promise.all([
      getPages(domain, 1),
      getSiteConfig(domain),
    ]);
    siteName = siteRes.data.name;
    siteUrl = `https://${siteRes.data.domain}`;

    const firstSlug = pagesRes.data?.[0]?.slug;
    if (firstSlug) {
      const pageRes = await getPage(domain, firstSlug);
      firstPage = pageRes.data;
    }
  } catch {
    // API unavailable
  }

  if (!firstPage) {
    return (
      <div className="page-content">
        <h1>Welcome to {siteName}</h1>
        <p>Content will appear here once pages have been created.</p>
      </div>
    );
  }

  const faqs = extractFAQSchema(firstPage.content);

  const orgSchema = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: siteName,
    url: siteUrl,
    logo: siteUrl + '/logo.png',
    sameAs: [],
  };

  const webPageSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: firstPage.meta_title || firstPage.title,
    description: firstPage.meta_description || '',
    url: siteUrl,
    isPartOf: {
      '@type': 'WebSite',
      name: siteName,
      url: siteUrl,
    },
    inLanguage: 'tr',
  };

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
    ],
  };

  const faqSchema =
    faqs.length > 0
      ? {
          '@context': 'https://schema.org',
          '@type': 'FAQPage',
          mainEntity: faqs.map((f) => ({
            '@type': 'Question',
            name: f.question,
            acceptedAnswer: {
              '@type': 'Answer',
              text: f.answer,
            },
          })),
        }
      : null;

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(orgSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(webPageSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />
      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
        />
      )}
      <div className="page-content">
        <h1>{firstPage.title}</h1>
        <div dangerouslySetInnerHTML={{ __html: firstPage.content }} />
      </div>
    </>
  );
}
