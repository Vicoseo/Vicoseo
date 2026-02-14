import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Image from 'next/image';
import { getCurrentDomain } from '@/lib/domain';
import { getPost, getSiteConfig } from '@/lib/api';

export const revalidate = 60;

interface BlogPostPageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({
  params,
}: BlogPostPageProps): Promise<Metadata> {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  try {
    const [res, siteRes] = await Promise.all([
      getPost(domain, slug),
      getSiteConfig(domain),
    ]);
    const post = res.data;
    const siteUrl = `https://${siteRes.data.domain}`;

    return {
      title: post.meta_title || post.title,
      description: post.meta_description || post.excerpt || undefined,
      alternates: {
        canonical: `${siteUrl}/blog/${slug}`,
      },
      openGraph: {
        title: post.meta_title || post.title,
        description: post.meta_description || post.excerpt || undefined,
        url: `${siteUrl}/blog/${slug}`,
        images: post.featured_image ? [{ url: post.featured_image }] : undefined,
        type: 'article',
        publishedTime: post.published_at,
        locale: 'tr_TR',
        siteName: siteRes.data.name,
      },
    };
  } catch {
    return {
      title: 'Yazı Bulunamadı',
    };
  }
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let post;
  let siteName = '';
  let siteUrl = '';

  try {
    const [res, siteRes] = await Promise.all([
      getPost(domain, slug),
      getSiteConfig(domain),
    ]);
    post = res.data;
    siteName = siteRes.data.name;
    siteUrl = `https://${siteRes.data.domain}`;
  } catch {
    notFound();
  }

  if (!post) {
    notFound();
  }

  const formattedDate = new Date(post.published_at).toLocaleDateString(
    'tr-TR',
    {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }
  );

  const articleJsonLd = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: post.title,
    description: post.meta_description || post.excerpt || '',
    image: post.featured_image || undefined,
    datePublished: post.published_at,
    url: `${siteUrl}/blog/${slug}`,
    publisher: {
      '@type': 'Organization',
      name: siteName,
      url: siteUrl,
    },
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': `${siteUrl}/blog/${slug}`,
    },
    inLanguage: 'tr',
  };

  return (
    <article className="page-content">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(articleJsonLd) }}
      />
      <header className="post-header">
        <h1 className="post-header__title">{post.title}</h1>
        <time className="post-header__date" dateTime={post.published_at}>
          {formattedDate}
        </time>
      </header>

      {post.featured_image && (
        <Image
          src={post.featured_image}
          alt={post.title}
          width={1200}
          height={630}
          className="post-featured-image"
          style={{ width: '100%', height: 'auto' }}
          priority
        />
      )}

      <div
        className="post-content"
        dangerouslySetInnerHTML={{ __html: post.content }}
      />
    </article>
  );
}
