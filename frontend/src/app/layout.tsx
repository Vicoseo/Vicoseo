import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getSiteConfig, getTopOffers } from '@/lib/api';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import LoginCtaBar from '@/components/LoginCtaBar';
import SponsorsBlock from '@/components/SponsorsBlock';
import { SiteConfig, TopOffer } from '@/types';
import './globals.css';

async function fetchLayoutData(): Promise<{
  site: SiteConfig | null;
  offers: TopOffer[];
}> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, offersRes] = await Promise.all([
      getSiteConfig(domain),
      getTopOffers(domain),
    ]);
    return {
      site: siteRes.data,
      offers: Array.isArray(offersRes.data) ? offersRes.data : [],
    };
  } catch {
    return { site: null, offers: [] };
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
    icons: site.favicon_url ? { icon: site.favicon_url } : undefined,
    metadataBase: new URL(siteUrl),
    alternates: {
      canonical: '/',
    },
    openGraph: {
      siteName: site.name,
      locale: 'tr_TR',
      type: 'website',
    },
    twitter: {
      card: 'summary_large_image',
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
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { site, offers } = await fetchLayoutData();

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
        {jsonLd && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
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
        {site && <Header site={site} />}
        <main style={{ flex: 1 }}>{children}</main>
        {site && <Footer site={site} />}
      </body>
    </html>
  );
}
