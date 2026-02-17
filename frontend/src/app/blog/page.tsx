import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { getCurrentDomain } from '@/lib/domain';
import { getPosts, getSiteConfig } from '@/lib/api';
import { Post, SiteConfig } from '@/types';

interface BlogPageProps {
  searchParams: Promise<{ page?: string }>;
}

export async function generateMetadata(): Promise<Metadata> {
  const domain = await getCurrentDomain();

  try {
    const siteRes = await getSiteConfig(domain);
    const siteUrl = `https://${siteRes.data.domain}`;

    return {
      title: 'Blog',
      description: `${siteRes.data.name} blog yazıları ve güncel makaleler.`,
      alternates: {
        canonical: `${siteUrl}/blog`,
      },
      openGraph: {
        title: `Blog | ${siteRes.data.name}`,
        description: `${siteRes.data.name} blog yazıları ve güncel makaleler.`,
        url: `${siteUrl}/blog`,
        type: 'website',
        locale: 'tr_TR',
        siteName: siteRes.data.name,
        images: [{ url: `${siteUrl}/storage/og-image.png`, width: 1200, height: 630, alt: siteRes.data.name }],
      },
      twitter: {
        card: 'summary_large_image',
        title: `Blog | ${siteRes.data.name}`,
        description: `${siteRes.data.name} blog yazıları ve güncel makaleler.`,
        images: [`${siteUrl}/storage/og-image.png`],
      },
    };
  } catch {
    return {
      title: 'Blog',
      description: 'Blog yazıları ve güncel makaleler.',
    };
  }
}

export default async function BlogPage({ searchParams }: BlogPageProps) {
  const resolvedSearchParams = await searchParams;
  const domain = await getCurrentDomain();
  const currentPage = Number(resolvedSearchParams.page) || 1;

  let posts: Post[] = [];
  let lastPage = 1;
  let site: SiteConfig | null = null;

  try {
    const [res, siteRes] = await Promise.all([
      getPosts(domain, currentPage),
      getSiteConfig(domain),
    ]);
    posts = res.data;
    lastPage = res.last_page;
    site = siteRes.data;
  } catch {
    // API unavailable - render empty state
  }

  const siteUrl = site ? `https://${site.domain}` : '';

  const collectionSchema = posts.length > 0 ? {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: `Blog | ${site?.name || ''}`,
    url: `${siteUrl}/blog`,
    isPartOf: { '@type': 'WebSite', name: site?.name, url: siteUrl },
    mainEntity: {
      '@type': 'ItemList',
      itemListElement: posts.map((post, idx) => ({
        '@type': 'ListItem',
        position: idx + 1,
        url: `${siteUrl}/blog/${post.slug}`,
        name: post.title,
      })),
    },
  } : null;

  if (posts.length === 0) {
    return (
      <div className="page-content">
        <h1>Blog</h1>
        <p>Henüz yayınlanmış blog yazısı bulunmamaktadır.</p>
      </div>
    );
  }

  return (
    <div className="page-content">
      {collectionSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
        />
      )}
      <h1>Blog</h1>
      <div className="blog-grid">
        {posts.map((post) => (
          <article key={post.id} className="blog-card">
            {post.featured_image && (
              <div className="blog-card__image-wrapper">
                <Image
                  src={post.featured_image}
                  alt={post.title}
                  fill
                  style={{ objectFit: 'cover' }}
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
                  loading="lazy"
                />
              </div>
            )}
            <div className="blog-card__body">
              <h2 className="blog-card__title">
                <Link href={`/blog/${post.slug}`}>{post.title}</Link>
              </h2>
              {post.excerpt && (
                <p className="blog-card__excerpt">{post.excerpt}</p>
              )}
              <time className="blog-card__date" dateTime={post.published_at}>
                {new Date(post.published_at).toLocaleDateString('tr-TR', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                })}
              </time>
            </div>
          </article>
        ))}
      </div>

      {lastPage > 1 && (
        <nav className="pagination" aria-label="Sayfalama">
          {currentPage > 1 && (
            <Link href={`/blog?page=${currentPage - 1}`}>Önceki</Link>
          )}
          {Array.from({ length: lastPage }, (_, i) => i + 1).map(
            (pageNum) => (
              <Link
                key={pageNum}
                href={`/blog?page=${pageNum}`}
                className={pageNum === currentPage ? 'active' : ''}
                aria-current={pageNum === currentPage ? 'page' : undefined}
              >
                {pageNum}
              </Link>
            )
          )}
          {currentPage < lastPage && (
            <Link href={`/blog?page=${currentPage + 1}`}>Sonraki</Link>
          )}
        </nav>
      )}
    </div>
  );
}
