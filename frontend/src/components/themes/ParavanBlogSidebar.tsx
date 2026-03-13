import Link from 'next/link';
import { SiteConfig, Post, Category } from '@/types';

interface ParavanBlogSidebarProps {
  site: SiteConfig;
  posts: Post[];
  categories: Category[];
  loginUrl: string;
}

export default function ParavanBlogSidebar({ site, posts, categories, loginUrl }: ParavanBlogSidebarProps) {
  const brandName = site.name;

  return (
    <aside className="pb-sidebar">
      <div className="pb-widget pb-widget--cta">
        <h3>{brandName}&apos;e Katıl!</h3>
        <p>Hoş geldin bonusu ile hemen oynamaya başla. İlk yatırıma %100 bonus!</p>
        <a href={loginUrl} className="pb-btn pb-btn--accent pb-btn--full" rel="nofollow">
          Hemen Kayıt Ol &rarr;
        </a>
      </div>

      {posts.length > 0 && (
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
      )}

      {categories.length > 0 && (
        <div className="pb-widget">
          <h3>Kategoriler</h3>
          <div className="pb-widget__links">
            {categories.map((cat) => (
              <Link key={cat.id} href={`/category/${cat.slug}`}>
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
  );
}
