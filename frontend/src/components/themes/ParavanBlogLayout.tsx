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
  const brandInitial = brandName.charAt(0).toUpperCase();

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

      {/* MAIN CONTENT */}
      <div className="pb-layout">
        <main className="pb-posts">
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
        </main>

        {/* SIDEBAR */}
        <aside className="pb-sidebar">
          <div className="pb-widget pb-widget--cta">
            <h3>{brandName}&apos;e Katıl!</h3>
            <p>Hoş geldin bonusu ile hemen oynamaya başla. İlk yatırıma %100 bonus!</p>
            <a href={loginUrl} className="pb-btn pb-btn--accent pb-btn--full" rel="nofollow">
              Hemen Kayıt Ol &rarr;
            </a>
          </div>

          <div className="pb-widget">
            <h3>Son Yazılar</h3>
            <div className="pb-widget__links">
              {posts.slice(0, 8).map((post) => (
                <Link key={post.id} href={`/blog/${post.slug}`}>
                  {post.title}
                </Link>
              ))}
            </div>
          </div>

          {categories.length > 0 && (
            <div className="pb-widget">
              <h3>Kategoriler</h3>
              <div className="pb-widget__links">
                {categories.map((cat) => (
                  <Link key={cat.id} href={`/kategori/${cat.slug}`}>
                    {cat.name} {cat.posts_count ? `(${cat.posts_count})` : ''}
                  </Link>
                ))}
              </div>
            </div>
          )}

          <div className="pb-widget">
            <h3>Etiketler</h3>
            <div className="pb-tag-cloud">
              {[
                `${brandName} Giriş`, 'Güncel Adres', 'Bonus', 'Slot Oyunları',
                'Canlı Casino', 'Promosyon', 'Spor Bahis', 'Canlı Bahis',
                'Freespin', 'VIP', 'Mobil Giriş',
              ].map((tag) => (
                <span key={tag} className="pb-tag">{tag}</span>
              ))}
            </div>
          </div>
        </aside>
      </div>

      {/* FOOTER */}
      <footer className="pb-footer">
        <div className="pb-footer__inner">
          <div className="pb-footer__top">
            <div className="pb-footer__brand">
              <h4>{brandName}</h4>
              <p>{brandName}, platform hakkında güncel bilgiler, giriş adresleri ve detaylı incelemeler sunan bağımsız bir rehber sitesidir. 18 yaşından küçüklerin bahis oynaması yasaktır.</p>
            </div>
            <div className="pb-footer__col">
              <h4>Sayfalar</h4>
              <Link href="/">Ana Sayfa</Link>
              <Link href="/blog">Blog</Link>
              {site.social_links?.telegram && (
                <a href={site.social_links.telegram} target="_blank" rel="noopener nofollow">Telegram</a>
              )}
            </div>
            <div className="pb-footer__col">
              <h4>Oyunlar</h4>
              <a href={loginUrl} rel="nofollow">Slot Oyunları</a>
              <a href={loginUrl} rel="nofollow">Canlı Casino</a>
              <a href={loginUrl} rel="nofollow">Spor Bahisleri</a>
            </div>
            <div className="pb-footer__col">
              <h4>İletişim</h4>
              {site.social_links?.telegram && (
                <a href={site.social_links.telegram} target="_blank" rel="noopener nofollow">Telegram</a>
              )}
              {site.social_links?.x && (
                <a href={site.social_links.x} target="_blank" rel="noopener nofollow">Twitter/X</a>
              )}
              {site.social_links?.support_email && (
                <a href={`mailto:${site.social_links.support_email}`}>Email</a>
              )}
            </div>
          </div>
          <div className="pb-footer__bottom">
            &copy; {new Date().getFullYear()} {brandName}. Tüm hakları saklıdır.<br />
            Bu site yalnızca bilgilendirme amaçlıdır. Kumar bağımlılık yapabilir, sorumlu oynayınız.
          </div>
        </div>
      </footer>
    </div>
  );
}
