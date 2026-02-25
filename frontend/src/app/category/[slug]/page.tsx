import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { getCurrentDomain } from '@/lib/domain';
import { getCategory, getSiteConfig } from '@/lib/api';

export const revalidate = 3600;

interface CategoryPageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: CategoryPageProps): Promise<Metadata> {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  try {
    const [catRes, siteRes] = await Promise.all([
      getCategory(domain, slug),
      getSiteConfig(domain),
    ]);

    const category = catRes.data;
    const siteUrl = `https://${siteRes.data.domain}`;
    const title = category.meta_title || `${category.name} | ${siteRes.data.name}`;
    const description = category.meta_description || category.description || `${category.name} - ${siteRes.data.name}`;

    return {
      title,
      description,
      alternates: {
        canonical: `${siteUrl}/category/${slug}`,
      },
      openGraph: {
        title,
        description,
        url: `${siteUrl}/category/${slug}`,
        type: 'website',
        locale: 'tr_TR',
        siteName: siteRes.data.name,
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
    return { title: 'Kategori Bulunamadı' };
  }
}

export default async function CategoryPage({ params }: CategoryPageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let category;
  let posts;
  let siteName = '';
  let siteUrl = '';

  try {
    const [catRes, siteRes] = await Promise.all([
      getCategory(domain, slug),
      getSiteConfig(domain),
    ]);
    category = catRes.data;
    posts = catRes.posts;
    siteName = siteRes.data.name;
    siteUrl = `https://${siteRes.data.domain}`;
  } catch {
    notFound();
  }

  if (!category) notFound();

  const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      { '@type': 'ListItem', position: 1, name: 'Ana Sayfa', item: siteUrl },
      { '@type': 'ListItem', position: 2, name: category.name, item: `${siteUrl}/category/${slug}` },
    ],
  };

  const collectionSchema = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: category.meta_title || category.name,
    description: category.meta_description || category.description || '',
    url: `${siteUrl}/category/${slug}`,
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
        dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
      />

      <div className="page-content">
        <h1>{category.name}</h1>

        {category.content && (
          <div
            className="category-description"
            dangerouslySetInnerHTML={{
              __html: category.content.replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"'),
            }}
          />
        )}

        {posts && posts.data && posts.data.length > 0 && (
          <div className="category-posts">
            <h2>{category.name} Yazıları</h2>
            <div className="category-posts-grid">
              {posts.data.map((post) => (
                <article key={post.id} className="category-post-card">
                  <Link href={`/blog/${post.slug}`}>
                    {post.featured_image && (
                      <img
                        src={post.featured_image.startsWith('http') ? post.featured_image : `${siteUrl}${post.featured_image}`}
                        alt={post.title}
                        loading="lazy"
                        className="category-post-card__image"
                      />
                    )}
                    <h3 className="category-post-card__title">{post.title}</h3>
                    {post.excerpt && (
                      <p className="category-post-card__excerpt">{post.excerpt}</p>
                    )}
                    <time className="category-post-card__date" dateTime={post.published_at}>
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
          </div>
        )}

        {(!posts || !posts.data || posts.data.length === 0) && !category.content && (
          <p>Bu kategoride henüz içerik bulunmamaktadır.</p>
        )}
      </div>
    </>
  );
}
