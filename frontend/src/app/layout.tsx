import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getSiteConfig, getTopOffers, getPages } from '@/lib/api';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import LoginCtaBar from '@/components/LoginCtaBar';
import SponsorsBlock from '@/components/SponsorsBlock';
import OfferCards from '@/components/OfferCards';
import { SiteConfig, TopOffer, Page } from '@/types';
import './globals.css';

async function fetchLayoutData(): Promise<{
  site: SiteConfig | null;
  offers: TopOffer[];
  pages: Page[];
}> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, offersRes, pagesRes] = await Promise.all([
      getSiteConfig(domain),
      getTopOffers(domain),
      getPages(domain, 1, 50),
    ]);
    return {
      site: siteRes.data,
      offers: Array.isArray(offersRes.data) ? offersRes.data : [],
      pages: pagesRes.data || [],
    };
  } catch {
    return { site: null, offers: [], pages: [] };
  }
}

export async function generateMetadata(): Promise<Metadata> {
  const { site } = await fetchLayoutData();

  if (!site) {
    return {
      title: 'Multi-Tenant CMS',
      description: 'Content Management System',
    };
  }

  const siteUrl = `https://${site.domain}`;

  return {
    title: {
      default: site.meta_title || site.name,
      template: `%s | ${site.name}`,
    },
    description: site.meta_description || `${site.name} - Online bahis ve casino platformu`,
    icons: {
      icon: [
        { url: site.favicon_url || '/storage/favicon.ico', type: 'image/x-icon' },
        { url: '/storage/icon-192.png', sizes: '192x192', type: 'image/png' },
        { url: '/storage/icon-512.png', sizes: '512x512', type: 'image/png' },
      ],
      apple: [
        { url: '/storage/apple-touch-icon.png', sizes: '180x180', type: 'image/png' },
      ],
    },
    metadataBase: new URL(siteUrl),
    alternates: {
      canonical: '/',
    },
    openGraph: {
      siteName: site.name,
      locale: 'tr_TR',
      type: 'website',
      images: [
        {
          url: '/storage/og-image.png',
          width: 1200,
          height: 630,
          alt: site.name,
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      images: ['/storage/og-image.png'],
    },
    robots: site?.noindex_mode
      ? { index: false, follow: false }
      : {
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
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { site, offers, pages } = await fetchLayoutData();

  const navPages = pages.filter(p => !['anasayfa', 'ana-sayfa'].includes(p.slug)).slice(0, 5);

  const primaryColor = site?.primary_color || '#007bff';
  const secondaryColor = site?.secondary_color || '#6c757d';
  const loginUrl = site?.login_url || site?.entry_url || '/go/login';

  const siteUrl = site ? `https://${site.domain}` : '';

  const jsonLd = site
    ? {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: site.name,
        url: siteUrl,
        description: site.meta_description || `${site.name} - Online bahis ve casino platformu`,
        inLanguage: 'tr',
        potentialAction: {
          '@type': 'SearchAction',
          target: {
            '@type': 'EntryPoint',
            urlTemplate: `${siteUrl}/blog?q={search_term_string}`,
          },
          'query-input': 'required name=search_term_string',
        },
        publisher: {
          '@type': 'Organization',
          name: site.name,
          url: siteUrl,
        },
      }
    : null;

  return (
    <html lang="tr">
      <head>
        {site?.ga_measurement_id && (
          <>
            <script
              async
              src={`https://www.googletagmanager.com/gtag/js?id=${site.ga_measurement_id}`}
            />
            <script
              dangerouslySetInnerHTML={{
                __html: `window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','${site.ga_measurement_id}');`,
              }}
            />
          </>
        )}
        {jsonLd && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
          />
        )}
        {site?.custom_css && (
          <style
            dangerouslySetInnerHTML={{
              __html: `.page-content { ${site.custom_css.replace(/<\/?script[^>]*>/gi, '')} }`,
            }}
          />
        )}
      </head>
      <body
        suppressHydrationWarning
        style={
          {
            '--primary-color': primaryColor,
            '--secondary-color': secondaryColor,
            display: 'flex',
            flexDirection: 'column',
            minHeight: '100vh',
          } as React.CSSProperties
        }
      >
        <LoginCtaBar loginUrl={loginUrl} />
        {site?.show_sponsors !== false && <SponsorsBlock offers={offers} />}
        {site?.sponsor_page_visible !== false && offers.length > 0 && (
          <OfferCards
            offers={offers}
            contactUrl={site?.sponsor_contact_url}
            contactText={site?.sponsor_contact_text}
          />
        )}
        {site && <Header site={site} pages={navPages} />}
        <main style={{ flex: 1 }}>{children}</main>
        {site && <Footer site={site} pages={navPages} />}
      </body>
    </html>
  );
}
