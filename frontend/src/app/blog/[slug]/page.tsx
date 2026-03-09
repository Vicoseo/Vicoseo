import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';
import { getCurrentDomain } from '@/lib/domain';
import { getPost, getPosts, getSiteConfig } from '@/lib/api';
import PostHero from '@/components/PostHero';

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

    const title = post.meta_title || post.title;
    const description = post.meta_description || post.excerpt || undefined;

    return {
      title,
      description,
      alternates: {
        canonical: `${siteUrl}/blog/${slug}`,
      },
      openGraph: {
        title,
        description,
        url: `${siteUrl}/blog/${slug}`,
        images: post.featured_image
          ? [{ url: post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image, width: 1200, height: 630, alt: post.title }]
          : [{ url: `${siteUrl}/storage/og-image.png`, width: 1200, height: 630, alt: siteRes.data.name }],
        type: 'article',
        publishedTime: post.published_at,
        modifiedTime: post.updated_at || post.published_at,
        locale: 'tr_TR',
        siteName: siteRes.data.name,
      },
      twitter: {
        card: 'summary_large_image',
        title,
        description,
        images: post.featured_image ? [post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image] : [`${siteUrl}/storage/og-image.png`],
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
      title: 'Yazı Bulunamadı',
    };
  }
}

function extractHeadings(html: string): { id: string; text: string; level: number }[] {
  const headings: { id: string; text: string; level: number }[] = [];
  const regex = /<h([2-3])[^>]*>(.*?)<\/h[2-3]>/gi;
  let match;
  while ((match = regex.exec(html)) !== null) {
    const text = match[2].replace(/<[^>]*>/g, '').trim();
    if (text) {
      const id = text
        .toLowerCase()
        .replace(/[^a-z0-9\u00C0-\u024F\u0400-\u04FF\u00E7\u011F\u0131\u00F6\u015F\u00FC]+/gi, '-')
        .replace(/^-|-$/g, '');
      headings.push({ id, text, level: parseInt(match[1]) });
    }
  }
  return headings;
}

function addHeadingIds(html: string, headings: { id: string; text: string; level: number }[]): string {
  let result = html;
  for (const h of headings) {
    const escapedText = h.text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(<h${h.level})([^>]*>)`, 'i');
    const searchRegex = new RegExp(`<h${h.level}[^>]*>[^<]*${escapedText}`, 'i');
    const found = result.match(searchRegex);
    if (found) {
      const idx = result.indexOf(found[0]);
      if (idx !== -1) {
        result = result.substring(0, idx) +
          found[0].replace(regex, `$1 id="${h.id}"$2`) +
          result.substring(idx + found[0].length);
      }
    }
  }
  return result;
}

function extractFAQs(html: string): { question: string; answer: string }[] {
  const faqs: { question: string; answer: string }[] = [];
  const faqRegex = /<strong>(.*?)<\/strong>.*?(?:<br\s*\/?>|<\/p>\s*<p>)([\s\S]*?)(?=<strong>|<\/ul>|<\/div>|$)/gi;
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

function calculateReadingTime(html: string): number {
  const text = html.replace(/<[^>]*>/g, '');
  const words = text.split(/\s+/).filter(Boolean).length;
  return Math.max(1, Math.ceil(words / 200));
}

function extractHowToSteps(html: string): { name: string; text: string }[] {
  const steps: { name: string; text: string }[] = [];
  // Extract from ordered lists
  const olRegex = /<ol[^>]*>([\s\S]*?)<\/ol>/gi;
  let olMatch;
  while ((olMatch = olRegex.exec(html)) !== null) {
    const liRegex = /<li[^>]*>([\s\S]*?)<\/li>/gi;
    let liMatch;
    while ((liMatch = liRegex.exec(olMatch[1])) !== null) {
      const text = liMatch[1].replace(/<[^>]*>/g, '').trim();
      if (text.length > 10) {
        const name = text.length > 80 ? text.substring(0, 80) + '...' : text;
        steps.push({ name, text });
      }
    }
    if (steps.length >= 3) break;
  }
  return steps.slice(0, 10);
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let post;
  let siteName = '';
  let siteUrl = '';
  let relatedPosts: { slug: string; title: string; excerpt?: string; published_at: string }[] = [];

  try {
    const [res, siteRes, postsRes] = await Promise.all([
      getPost(domain, slug),
      getSiteConfig(domain),
      getPosts(domain, 1, 50),
    ]);
    post = res.data;
    siteName = siteRes.data.name;
    siteUrl = `https://${siteRes.data.domain}`;

    // Get related posts (excluding current post, max 6)
    const allPosts = postsRes.data || [];
    relatedPosts = allPosts
      .filter((p) => p.slug !== slug)
      .slice(0, 6)
      .map((p) => ({ slug: p.slug, title: p.title, excerpt: p.excerpt || undefined, published_at: p.published_at }));
  } catch {
    notFound();
  }

  if (!post) {
    notFound();
  }

  const formattedDate = new Date(post.published_at).toLocaleDateString(
    'tr-TR',
    { year: 'numeric', month: 'long', day: 'numeric' }
  );

  const readingTime = calculateReadingTime(post.content);
  const headings = extractHeadings(post.content);
  const contentWithIds = addHeadingIds(
    post.content.replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"'),
    headings
  );
  const faqs = extractFAQs(post.content);

  const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      { '@type': 'ListItem', position: 1, name: 'Ana Sayfa', item: siteUrl },
      { '@type': 'ListItem', position: 2, name: 'Blog', item: `${siteUrl}/blog` },
      { '@type': 'ListItem', position: 3, name: post.title, item: `${siteUrl}/blog/${slug}` },
    ],
  };

  const articleJsonLd = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: post.title,
    description: post.meta_description || post.excerpt || '',
    image: post.featured_image ? (post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image) : undefined,
    datePublished: post.published_at,
    dateModified: post.updated_at || post.published_at,
    author: {
      '@type': 'Organization',
      name: siteName,
      url: siteUrl,
    },
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
    wordCount: post.content.replace(/<[^>]*>/g, '').split(/\s+/).filter(Boolean).length,
  };

  const faqSchema = faqs.length > 0 ? {
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
  } : null;

  // HowTo schema for guide-type posts (kayit, giris, para-yatirma)
  const isGuidePost = /(-kayit|-giris|-uyelik|-para-yatirma|-kurulum|-nasil)/i.test(slug);
  const howToSteps = isGuidePost ? extractHowToSteps(post.content) : [];
  const howToSchema = isGuidePost && howToSteps.length >= 2 ? {
    '@context': 'https://schema.org',
    '@type': 'HowTo',
    name: post.title,
    description: post.meta_description || post.excerpt || '',
    step: howToSteps.map((step, i) => ({
      '@type': 'HowToStep',
      position: i + 1,
      name: step.name,
      text: step.text,
    })),
  } : null;

  return (
    <article className="page-content">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(articleJsonLd) }}
      />
      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
        />
      )}
      {howToSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(howToSchema) }}
        />
      )}

      {/* Visible Breadcrumbs */}
      <nav className="breadcrumbs" aria-label="Breadcrumb">
        <ol>
          <li><Link href="/">Ana Sayfa</Link></li>
          <li><Link href="/blog">Blog</Link></li>
          <li aria-current="page">{post.title}</li>
        </ol>
      </nav>

      {post.hero?.background ? (
        <PostHero
          title={post.title}
          hero={post.hero}
          publishedAt={post.published_at}
          readingTime={readingTime}
          categoryName={post.category?.name}
          featuredImage={post.featured_image}
          siteUrl={siteUrl}
        />
      ) : (
        <>
          <header className="post-header">
            <h1 className="post-header__title">{post.title}</h1>
            <div className="post-meta">
              <time className="post-header__date" dateTime={post.published_at}>
                {formattedDate}
              </time>
              <span className="post-reading-time">{readingTime} dk okuma</span>
            </div>
          </header>

          {post.featured_image && (
            <Image
              src={post.featured_image.startsWith('/') ? `${siteUrl}${post.featured_image}` : post.featured_image}
              alt={post.title}
              width={1200}
              height={630}
              className="post-featured-image"
              style={{ width: '100%', height: 'auto' }}
              priority
            />
          )}
        </>
      )}

      {/* Table of Contents */}
      {headings.length >= 3 && (
        <nav className="toc" aria-label="İçindekiler">
          <details open>
            <summary className="toc__title">İçindekiler</summary>
            <ol className="toc__list">
              {headings.map((h, i) => (
                <li key={i} className={h.level === 3 ? 'toc__item--sub' : ''}>
                  <a href={`#${h.id}`}>{h.text}</a>
                </li>
              ))}
            </ol>
          </details>
        </nav>
      )}

      <div
        className="post-content"
        dangerouslySetInnerHTML={{ __html: contentWithIds }}
      />

      {/* Internal Links */}
      <nav className="post-internal-links" aria-label="İlgili bağlantılar">
        <p>
          <a href="/">{siteName} güncel giriş</a> adresinden siteye ulaşabilirsiniz.
          Daha fazla bilgi için <a href="/blog">{siteName} blog</a> sayfamızı ziyaret edin.
        </p>
      </nav>

      {/* Related Posts */}
      {relatedPosts.length > 0 && (
        <section className="related-posts" aria-label="İlgili Yazılar">
          <h2 className="related-posts__title">İlgili Yazılar</h2>
          <div className="related-posts__grid">
            {relatedPosts.map((rp) => (
              <article key={rp.slug} className="related-post-card">
                <Link href={`/blog/${rp.slug}`} className="related-post-card__link">
                  <h3 className="related-post-card__title">{rp.title}</h3>
                  {rp.excerpt && (
                    <p className="related-post-card__excerpt">{rp.excerpt}</p>
                  )}
                  <time className="related-post-card__date" dateTime={rp.published_at}>
                    {new Date(rp.published_at).toLocaleDateString('tr-TR', {
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
    </article>
  );
}
