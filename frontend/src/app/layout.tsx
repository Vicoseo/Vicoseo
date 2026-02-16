import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getSiteConfig, getTopOffers, getSponsors } from '@/lib/api';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import LoginCtaBar from '@/components/LoginCtaBar';
import SponsorsBlock from '@/components/SponsorsBlock';
import Sponsors from '@/components/Sponsors';
import { SiteConfig, TopOffer, Sponsor } from '@/types';
import './globals.css';

async function fetchLayoutData(): Promise<{
  site: SiteConfig | null;
  offers: TopOffer[];
  sponsors: Sponsor[];
}> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, offersRes, sponsorsRes] = await Promise.all([
      getSiteConfig(domain),
      getTopOffers(domain),
      getSponsors(),
    ]);
    return {
      site: siteRes.data,
      offers: Array.isArray(offersRes.data) ? offersRes.data : [],
      sponsors: Array.isArray(sponsorsRes.data) ? sponsorsRes.data : [],
    };
  } catch {
    return { site: null, offers: [], sponsors: [] };
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
  const { site, offers, sponsors } = await fetchLayoutData();

  const primaryColor = site?.primary_color || '#007bff';
  const secondaryColor = site?.secondary_color || '#6c757d';
  const loginUrl = site?.login_url || site?.entry_url || '/go/login';

  const jsonLd = site
    ? {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: site.name,
        url: `https://${site.domain}`,
        description: site.meta_description || `${site.name} - Online bahis ve casino platformu`,
        inLanguage: 'tr',
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
        {site?.sponsor_page_visible !== false && sponsors.length > 0 && (
          <Sponsors
            sponsors={sponsors}
            contactUrl={site?.sponsor_contact_url}
            contactText={site?.sponsor_contact_text}
          />
        )}
        {site && <Header site={site} />}
        <main style={{ flex: 1 }}>{children}</main>
        {site && <Footer site={site} />}
      </body>
    </html>
  );
}
