import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getSiteConfig, getTopOffers, getPages, getPosts, getCategories } from '@/lib/api';
import Link from 'next/link';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import LoginCtaBar from '@/components/LoginCtaBar';
import SponsorsBlock from '@/components/SponsorsBlock';
import OfferCards from '@/components/OfferCards';
import { SiteConfig, TopOffer, Page, Post, Category } from '@/types';
import './globals.css';

async function fetchLayoutData(): Promise<{
  site: SiteConfig | null;
  offers: TopOffer[];
  pages: Page[];
  posts: Post[];
  categories: Category[];
}> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, offersRes, pagesRes, postsRes, catRes] = await Promise.all([
      getSiteConfig(domain),
      getTopOffers(domain),
      getPages(domain, 1, 50),
      getPosts(domain, 1, 50),
      getCategories(domain).catch(() => ({ data: [] })),
    ]);
    return {
      site: siteRes.data,
      offers: Array.isArray(offersRes.data) ? offersRes.data : [],
      pages: pagesRes.data || [],
      posts: postsRes.data || [],
      categories: Array.isArray(catRes.data) ? catRes.data : [],
    };
  } catch {
    return { site: null, offers: [], pages: [], posts: [], categories: [] };
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
    icons: (() => {
      // Derive icon paths from favicon_url directory if site-specific
      const faviconUrl = site.favicon_url || '/storage/favicon.ico';
      const faviconDir = faviconUrl.includes('/favicons/')
        ? faviconUrl.substring(0, faviconUrl.lastIndexOf('/'))
        : null;

      return {
        icon: [
          { url: faviconUrl, type: 'image/x-icon', sizes: '48x48' },
          { url: faviconDir ? `${faviconDir}/favicon-32.png` : faviconUrl, type: 'image/png', sizes: '32x32' },
          { url: faviconDir ? `${faviconDir}/icon-192.png` : '/storage/icon-192.png', sizes: '192x192', type: 'image/png' },
          { url: faviconDir ? `${faviconDir}/icon-512.png` : '/storage/icon-512.png', sizes: '512x512', type: 'image/png' },
        ],
        apple: [
          { url: faviconDir ? `${faviconDir}/apple-touch-icon.png` : '/storage/apple-touch-icon.png', sizes: '180x180', type: 'image/png' },
        ],
      };
    })(),
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
  const { site, offers, pages, posts, categories } = await fetchLayoutData();

  const navPages = pages.filter(p => !['anasayfa', 'ana-sayfa'].includes(p.slug)).slice(0, 5);

  const primaryColor = site?.primary_color || '#007bff';
  const secondaryColor = site?.secondary_color || '#6c757d';
  const loginUrl = site?.login_url || site?.entry_url || '/go/login';
  const backgroundUrl = site?.background_url || null;

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
            ...(backgroundUrl
              ? {
                  backgroundImage: `url(${process.env.NEXT_PUBLIC_API_URL?.replace('/api/v1', '') || ''}${backgroundUrl})`,
                  backgroundSize: 'cover',
                  backgroundPosition: 'center top',
                  backgroundRepeat: 'no-repeat',
                  backgroundAttachment: 'fixed',
                }
              : {}),
          } as React.CSSProperties
        }
      >
        <LoginCtaBar loginUrl={loginUrl} />
        {!!site?.show_sponsors && <SponsorsBlock offers={offers} />}
        {!!site?.sponsor_page_visible && offers.length > 0 && (
          <OfferCards
            offers={offers}
            contactUrl={site?.sponsor_contact_url}
            contactText={site?.sponsor_contact_text}
          />
        )}
        {site && <Header site={site} pages={navPages} />}
        <main style={{ flex: 1 }}>{children}</main>
        {posts.length > 0 && (
          <section className="recent-posts-section">
            <h2 className="recent-posts-title">Son Yazılar</h2>
            <div className="recent-posts-grid">
              {posts.map((post) => (
                <article key={post.id} className="recent-post-card">
                  <Link href={`/blog/${post.slug}`} className="recent-post-link">
                    <h3 className="recent-post-card__title">{post.title}</h3>
                    {post.excerpt && (
                      <p className="recent-post-card__excerpt">{post.excerpt}</p>
                    )}
                    <time className="recent-post-card__date" dateTime={post.published_at}>
                      {new Date(post.published_at).toLocaleDateString('tr-TR', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                      })}
                    </time>
                  </Link>
                </article>
              ))}
            </div>
          </section>
        )}
        {site && <Footer site={site} pages={navPages} posts={posts} categories={categories} />}
      </body>
    </html>
  );
}
