import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { getCurrentDomain } from '@/lib/domain';
import { getPosts } from '@/lib/api';
import { Post } from '@/types';

interface BlogPageProps {
  searchParams: Promise<{ page?: string }>;
}

export const metadata: Metadata = {
  title: 'Blog',
  description: 'Read our latest blog posts and articles.',
};

export default async function BlogPage({ searchParams }: BlogPageProps) {
  const resolvedSearchParams = await searchParams;
  const domain = await getCurrentDomain();
  const currentPage = Number(resolvedSearchParams.page) || 1;

  let posts: Post[] = [];
  let lastPage = 1;

  try {
    const res = await getPosts(domain, currentPage);
    posts = res.data;
    lastPage = res.last_page;
  } catch {
    // API unavailable - render empty state
  }

  if (posts.length === 0) {
    return (
      <div className="page-content">
        <h1>Blog</h1>
        <p>No blog posts have been published yet. Check back soon!</p>
      </div>
    );
  }

  return (
    <div className="page-content">
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
                {new Date(post.published_at).toLocaleDateString('en-US', {
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
        <nav className="pagination">
          {currentPage > 1 && (
            <Link href={`/blog?page=${currentPage - 1}`}>Previous</Link>
          )}
          {Array.from({ length: lastPage }, (_, i) => i + 1).map(
            (pageNum) => (
              <Link
                key={pageNum}
                href={`/blog?page=${pageNum}`}
                className={pageNum === currentPage ? 'active' : ''}
              >
                {pageNum}
              </Link>
            )
          )}
          {currentPage < lastPage && (
            <Link href={`/blog?page=${currentPage + 1}`}>Next</Link>
          )}
        </nav>
      )}
    </div>
  );
}
