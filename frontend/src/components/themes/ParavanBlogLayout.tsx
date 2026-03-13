import Link from 'next/link';
import { SiteConfig, Post, Category, Page } from '@/types';

interface ParavanBlogLayoutProps {
  site: SiteConfig;
  posts: Post[];
  categories: Category[];
  page: Page | null;
}

export default function ParavanBlogLayout({ site, posts, categories, page }: ParavanBlogLayoutProps) {
  const siteUrl = `https://${site.domain}`;
  const loginUrl = site.login_url || site.entry_url || '/';
  const brandName = site.name;

  return (
    <div className="pb-theme">
      {/* HERO */}
      <section className="pb-hero">
        <div className="pb-hero__inner">
          <div className="pb-hero__content">
            <h2 className="pb-hero__title">
              {brandName} <span className="pb-hero__highlight">Güncel Giriş</span> Adresi ve Bonus Rehberi
            </h2>
            <p className="pb-hero__desc">
              {brandName} platformunun en güncel giriş adresi, bonus kampanyaları, slot oyunları ve canlı casino incelemeleri. Güvenli erişim için doğru adrestesiniz.
            </p>
            <a href={loginUrl} className="pb-btn pb-btn--accent" rel="nofollow">
              {brandName}&apos;e Git &rarr;
            </a>
            <div className="pb-hero__stats">
              <div className="pb-hero__stat">
                <div className="pb-hero__stat-num">500+</div>
                <div className="pb-hero__stat-label">Slot Oyunu</div>
              </div>
              <div className="pb-hero__stat">
                <div className="pb-hero__stat-num">50+</div>
                <div className="pb-hero__stat-label">Canlı Masa</div>
              </div>
              <div className="pb-hero__stat">
                <div className="pb-hero__stat-num">7/24</div>
                <div className="pb-hero__stat-label">Canlı Destek</div>
              </div>
              <div className="pb-hero__stat">
                <div className="pb-hero__stat-num">%100</div>
                <div className="pb-hero__stat-label">Hoş Geldin</div>
              </div>
            </div>
          </div>
          {posts.length > 0 && posts[0].featured_image && (
            <div className="pb-hero__img">
              <img
                src={posts[0].featured_image.startsWith('http') ? posts[0].featured_image : `${siteUrl}${posts[0].featured_image}`}
                alt={posts[0].title}
                width={600}
                height={320}
                loading="eager"
              />
            </div>
          )}
        </div>
      </section>

      {/* Page content (if exists) */}
      {page && (
        <div className="pb-page-content">
          <h1>{page.title}</h1>
          <div dangerouslySetInnerHTML={{ __html: page.content
            .replace(/<h1([^>]*)>/gi, '<h2$1>')
            .replace(/<\/h1>/gi, '</h2>')
            .replace(/<img(?![^>]*loading=)/gi, '<img loading="lazy"')
          }} />
        </div>
      )}

      {/* POSTS GRID */}
      <div className="pb-posts">
        <h2 className="pb-posts__title">Son Yazılar</h2>

        {posts.map((post) => {
          const categoryName = post.category?.name || brandName;
          const readTime = Math.max(2, Math.ceil((post.content?.length || 500) / 1000));

          return (
            <article key={post.id} className="pb-card">
              {post.featured_image && (
                <div className="pb-card__image">
                  <Link href={`/blog/${post.slug}`}>
                    <img
                      src={post.featured_image.startsWith('http') ? post.featured_image : `${siteUrl}${post.featured_image}`}
                      alt={post.title}
                      width={800}
                      height={280}
                      loading="lazy"
                    />
                  </Link>
                  {post.category?.name && (
                    <span className="pb-card__badge">{post.category.name.toUpperCase()}</span>
                  )}
                </div>
              )}
              <div className="pb-card__body">
                <div className="pb-card__meta">
                  <time dateTime={post.published_at}>
                    {new Date(post.published_at).toLocaleDateString('tr-TR', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    })}
                  </time>
                  <span>{categoryName}</span>
                  <span>{readTime} dk okuma</span>
                </div>
                <h3 className="pb-card__title">
                  <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                </h3>
                {post.excerpt && (
                  <p className="pb-card__excerpt">{post.excerpt}</p>
                )}
                <Link href={`/blog/${post.slug}`} className="pb-card__readmore">
                  Devamını oku &rarr;
                </Link>
              </div>
            </article>
          );
        })}

        {posts.length === 0 && (
          <div className="pb-card">
            <div className="pb-card__body">
              <p>Henüz yazı bulunmamaktadır. İçerikler yakında eklenecektir.</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
