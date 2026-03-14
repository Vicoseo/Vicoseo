import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getPages, getPage, getSiteConfig, getPosts, getCategories } from '@/lib/api';
import { SiteConfig } from '@/types';
import EarningsSection from '@/components/EarningsSection';
import PromotionSlider from '@/components/PromotionSlider';
import PromotionCards from '@/components/PromotionCards';
import ServiceCards from '@/components/ServiceCards';
import ParavanBlogLayout from '@/components/themes/ParavanBlogLayout';


export async function generateMetadata(): Promise<Metadata> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, pagesRes] = await Promise.all([
      getSiteConfig(domain),
      getPages(domain, 1),
    ]);

    const firstSlug = pagesRes.data?.[0]?.slug;
    let firstPage = pagesRes.data?.[0] || null;

    // Fetch full page data to get meta_title, meta_description, meta_keywords
    if (firstSlug) {
      try {
        const pageRes = await getPage(domain, firstSlug);
        if (pageRes.data) firstPage = pageRes.data;
      } catch { /* use summary */ }
    }

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

function buildSameAs(site: SiteConfig): string[] {
  const links: string[] = [];
  const social = site.social_links;
  if (!social) return links;
  if (social.telegram) links.push(social.telegram);
  if (social.instagram) links.push(social.instagram);
  if (social.x) links.push(social.x);
  if (social.youtube) links.push(social.youtube);
  if (social.tiktok) links.push(social.tiktok);
  return links;
}

export default async function HomePage() {
  const domain = await getCurrentDomain();

  let firstPage = null;
  let siteName = 'our site';
  let siteUrl = '';
  let site: SiteConfig | null = null;
  try {
    const [pagesRes, siteRes] = await Promise.all([
      getPages(domain, 1),
      getSiteConfig(domain),
    ]);
    site = siteRes.data;
    siteName = site.name;
    siteUrl = `https://${site.domain}`;

    const firstSlug = pagesRes.data?.[0]?.slug;
    if (firstSlug) {
      const pageRes = await getPage(domain, firstSlug);
      firstPage = pageRes.data;
    }
  } catch {
    // API unavailable
  }

  // Paravan Blog theme: completely different layout
  if (site?.theme_template === 'paravan-blog') {
    let blogPosts: any[] = [];
    let blogCategories: any[] = [];
    try {
      const [postsRes, catRes] = await Promise.all([
        getPosts(domain, 1, 20),
        getCategories(domain).catch(() => ({ data: [] })),
      ]);
      blogPosts = postsRes.data || [];
      blogCategories = Array.isArray(catRes.data) ? catRes.data : [];
    } catch { /* fallback empty */ }

    return (
      <ParavanBlogLayout
        site={site}
        posts={blogPosts}
        categories={blogCategories}
        page={firstPage}
      />
    );
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

  const logoUrl = site?.logo_url
    ? (site.logo_url.startsWith('http') ? site.logo_url : `${siteUrl}${site.logo_url}`)
    : `${siteUrl}/storage/og-image.png`;

  const orgSchema = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: siteName,
    url: siteUrl,
    logo: logoUrl,
    image: logoUrl,
    sameAs: site ? buildSameAs(site) : [],
    ...(site?.social_links?.support_email && {
      contactPoint: {
        '@type': 'ContactPoint',
        email: site.social_links.support_email,
        contactType: 'customer service',
        availableLanguage: 'Turkish',
      },
    }),
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
      {siteUrl && <link rel="amphtml" href={`${siteUrl}/amp`} />}
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
        <h1>{firstPage.title}</h1>
        <div dangerouslySetInnerHTML={{ __html: firstPage.content
          .replace(/<h1([^>]*)>/gi, '<h2$1>')
          .replace(/<\/h1>/gi, '</h2>')
          .replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"')
          .replace(/<img(?![^>]*decoding=)/gi, '<img decoding="async"')
        }} />
      </div>

      {site && site.promotion_cards && site.promotion_cards.length > 0 && (
        <div className="promo-cards-wrapper">
          <h2 className="promo-cards-heading">Promosyonlar</h2>
          <PromotionCards
            cards={site.promotion_cards}
            domain={site.domain}
            loginUrl={site.login_url || site.entry_url || undefined}
          />
        </div>
      )}

      <ServiceCards />

      {site && site.earnings && site.earnings.length > 0 && (
        <EarningsSection earnings={site.earnings} domain={site.domain} />
      )}

    </>
  );
}
