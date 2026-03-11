import type { Metadata, Viewport } from 'next';
import { headers } from 'next/headers';
import { getCurrentDomain } from '@/lib/domain';
import { getSiteConfig, getTopOffers, getPages, getPosts, getPopularPosts, getCategories } from '@/lib/api';
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
  popularPosts: Post[];
  categories: Category[];
}> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, offersRes, pagesRes, postsRes, popularRes, catRes] = await Promise.all([
      getSiteConfig(domain),
      getTopOffers(domain),
      getPages(domain, 1, 50),
      getPosts(domain, 1, 50),
      getPopularPosts(domain, 6).catch(() => ({ data: [], has_analytics_data: false })),
      getCategories(domain).catch(() => ({ data: [] })),
    ]);
    return {
      site: siteRes.data,
      offers: Array.isArray(offersRes.data) ? offersRes.data : [],
      pages: pagesRes.data || [],
      posts: postsRes.data || [],
      popularPosts: popularRes.data || [],
      categories: Array.isArray(catRes.data) ? catRes.data : [],
    };
  } catch {
    return { site: null, offers: [], pages: [], posts: [], popularPosts: [], categories: [] };
  }
}

export async function generateViewport(): Promise<Viewport> {
  const { site } = await fetchLayoutData();

  return {
    width: 'device-width',
    initialScale: 1,
    maximumScale: 5,
    themeColor: site?.primary_color || '#0a0a1a',
  };
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
  const { site, offers, pages, posts, popularPosts, categories } = await fetchLayoutData();

  // BTK bot protection: check sanitize header from middleware/nginx
  const headersList = await headers();
  const sanitize = headersList.get('x-sanitize-content') === 'true';

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
        description: site.meta_description || `${site.name} - Online platform`,
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

  const siteNavSchema = site && navPages.length > 0
    ? {
        '@context': 'https://schema.org',
        '@type': 'ItemList',
        itemListElement: [
          {
            '@type': 'SiteNavigationElement',
            position: 1,
            name: 'Ana Sayfa',
            url: siteUrl,
          },
          ...navPages.map((page, i) => ({
            '@type': 'SiteNavigationElement',
            position: i + 2,
            name: page.title,
            url: `${siteUrl}/${page.slug}`,
          })),
          {
            '@type': 'SiteNavigationElement',
            position: navPages.length + 2,
            name: 'Blog',
            url: `${siteUrl}/blog`,
          },
        ],
      }
    : null;

  return (
    <html lang="tr">
      <head>
        <link rel="dns-prefetch" href="https://www.googletagmanager.com" />
        <link rel="preconnect" href="https://www.googletagmanager.com" crossOrigin="" />
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
        {siteNavSchema && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(siteNavSchema) }}
          />
        )}
        {site?.custom_css && (
          <style
            dangerouslySetInnerHTML={{
              __html: site.custom_css.replace(/<\/?script[^>]*>/gi, ''),
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
                  backgroundAttachment: 'scroll',
                }
              : {}),
          } as React.CSSProperties
        }
      >
        {!sanitize && <div className="login-cta-desktop-only"><LoginCtaBar loginUrl={loginUrl} /></div>}
        {!sanitize && !!site?.show_sponsors && <SponsorsBlock offers={offers} />}
        {!sanitize && !!site?.sponsor_page_visible && offers.length > 0 && (
          <OfferCards
            offers={offers}
            contactUrl={site?.sponsor_contact_url}
            contactText={site?.sponsor_contact_text}
          />
        )}
        {site && <Header site={site} pages={navPages} loginUrl={loginUrl} />}
        <main style={{ flex: 1 }}>{children}</main>
        {!sanitize && site?.domain?.includes('locabe') && (
          <section className="locatv-banner">
            <div className="locatv-banner__inner">
              <video
                className="locatv-banner__video"
                autoPlay
                loop
                muted
                playsInline
                preload="metadata"
              >
                <source src={`${siteUrl}/storage/posts/locabet/locatv-500tl.webm`} type="video/webm" />
              </video>
              <div className="locatv-banner__content">
                <h2 className="locatv-banner__title">Canlı TV İzle</h2>
                <p className="locatv-banner__desc">Sadece 500 TL yatırım ile canlı maç ve etkinlikleri anında izlemeye başla! Locabet TV ile spor karşılaşmalarını HD kalitede, kesintisiz takip et.</p>
                <a href={loginUrl} className="locatv-banner__cta" rel="nofollow">Hemen Yatır &amp; İzle</a>
              </div>
            </div>
          </section>
        )}
        {popularPosts.length > 0 && (
          <section className="featured-posts-section">
            <h2 className="featured-posts-title">Öne Çıkan Yazılar</h2>
            <div className="featured-posts-grid">
              {popularPosts.map((post) => (
                <article key={post.id} className="featured-post-card">
                  <Link href={`/blog/${post.slug}`} className="featured-post-link">
                    {post.featured_image && (
                      <img
                        src={post.featured_image.startsWith('http') ? post.featured_image : `${siteUrl}${post.featured_image}`}
                        alt={post.title}
                        className="featured-post-card__image"
                        width={768}
                        height={400}
                        loading="lazy"
                      />
                    )}
                    {(post.popularity_score ?? 0) > 0 && (
                      <span className="featured-post-card__badge">Popüler</span>
                    )}
                    <h3 className="featured-post-card__title">{post.title}</h3>
                    {post.excerpt && (
                      <p className="featured-post-card__excerpt">{post.excerpt}</p>
                    )}
                    <time className="featured-post-card__date" dateTime={post.published_at}>
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
        {posts.length > 0 && (
          <section className="recent-posts-section">
            <h2 className="recent-posts-title">Son Yazılar</h2>
            <div className="recent-posts-grid">
              {posts.map((post) => (
                <article key={post.id} className="recent-post-card">
                  <Link href={`/blog/${post.slug}`} className="recent-post-link">
                    {post.featured_image && (
                      <img
                        src={post.featured_image.startsWith('http') ? post.featured_image : `${siteUrl}${post.featured_image}`}
                        alt={post.title}
                        className="recent-post-card__image"
                        width={768}
                        height={400}
                        loading="lazy"
                      />
                    )}
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
