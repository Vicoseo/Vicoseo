import Image from 'next/image';
import type { HeroData } from '@/types';

interface PostHeroProps {
  title: string;
  hero: HeroData;
  publishedAt: string;
  readingTime: number;
  categoryName?: string;
  featuredImage?: string | null;
  siteUrl: string;
}

export default function PostHero({
  title,
  hero,
  publishedAt,
  readingTime,
  categoryName,
  featuredImage,
  siteUrl,
}: PostHeroProps) {
  if (!hero.background) return null;

  const formattedDate = new Date(publishedAt).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  const isLeft = hero.layout === 'left';
  const showFeaturedInHero = isLeft && hero.featured_image_in_hero && featuredImage;
  const resolvedImage = featuredImage
    ? featuredImage.startsWith('/') ? `${siteUrl}${featuredImage}` : featuredImage
    : null;

  return (
    <section className={`post-hero ${isLeft ? 'post-hero--left' : 'post-hero--centered'}`}>
      <Image
        src={hero.background.image_url.startsWith('/') ? `${siteUrl}${hero.background.image_url}` : hero.background.image_url}
        alt=""
        fill
        className="post-hero__bg"
        priority
        sizes="100vw"
      />
      <div
        className="post-hero__overlay"
        style={{
          backgroundColor: hero.overlay_color,
          mixBlendMode: hero.overlay_blend as React.CSSProperties['mixBlendMode'],
        }}
      />

      <div className="post-hero__inner">
        <div className="post-hero__text">
          {hero.badge.show && hero.badge.text && (
            <span
              className="post-hero__badge"
              style={{ backgroundColor: hero.accent_color }}
            >
              {hero.badge.text}
            </span>
          )}

          <h1 className="post-hero__title">{title}</h1>

          {hero.slogan && (
            <p className="post-hero__slogan">{hero.slogan}</p>
          )}

          <div className="post-hero__meta">
            {hero.show_date && (
              <time dateTime={publishedAt}>{formattedDate}</time>
            )}
            {hero.show_reading_time && (
              <span>{readingTime} dk okuma</span>
            )}
            {hero.show_category && categoryName && (
              <span>{categoryName}</span>
            )}
          </div>

          {hero.cta.show && hero.cta.url && (
            <a
              href={hero.cta.url}
              className="post-hero__cta"
              style={{ backgroundColor: hero.accent_color }}
              target="_blank"
              rel="noopener noreferrer nofollow"
            >
              {hero.cta.text || 'Hemen Giris Yap'}
              <span className="post-hero__cta-arrow">&rarr;</span>
            </a>
          )}
        </div>

        {showFeaturedInHero && resolvedImage && (
          <div className="post-hero__featured">
            <Image
              src={resolvedImage}
              alt={title}
              width={400}
              height={260}
              className="post-hero__featured-img"
              priority
            />
          </div>
        )}
      </div>
    </section>
  );
}
