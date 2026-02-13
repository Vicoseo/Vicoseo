import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Image from 'next/image';
import { getCurrentDomain } from '@/lib/domain';
import { getPost } from '@/lib/api';

// ISR: revalidate every 60 seconds
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
    const res = await getPost(domain, slug);
    const post = res.data;

    return {
      title: post.meta_title || post.title,
      description: post.meta_description || post.excerpt || undefined,
      openGraph: {
        title: post.meta_title || post.title,
        description: post.meta_description || post.excerpt || undefined,
        images: post.featured_image ? [{ url: post.featured_image }] : undefined,
        type: 'article',
        publishedTime: post.published_at,
      },
    };
  } catch {
    return {
      title: 'Post Not Found',
    };
  }
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let post;

  try {
    const res = await getPost(domain, slug);
    post = res.data;
  } catch {
    notFound();
  }

  if (!post) {
    notFound();
  }

  const formattedDate = new Date(post.published_at).toLocaleDateString(
    'en-US',
    {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }
  );

  return (
    <article className="page-content">
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
