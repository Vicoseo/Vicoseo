import Image from 'next/image';
import type { HeroData } from '@/types';

interface PostHeroProps {
  title: string;
  hero: HeroData;
  publishedAt: string;
  readingTime: number;
  siteUrl: string;
  ctaUrl?: string;
}

export default function PostHero({
  title,
  hero,
  publishedAt,
  readingTime,
  siteUrl,
  ctaUrl,
}: PostHeroProps) {
  if (!hero.background) return null;

  const formattedDate = new Date(publishedAt).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  return (
    <section className="post-hero post-hero--centered">
      <Image
        src={hero.background.image_url.startsWith('/') ? `${siteUrl}${hero.background.image_url}` : hero.background.image_url}
        alt=""
        width={1920}
        height={600}
        className="post-hero__bg"
        priority
        sizes="100vw"
      />
      <div
        className="post-hero__overlay"
        style={{ backgroundColor: hero.overlay_color }}
      />

      <div className="post-hero__inner">
        <div className="post-hero__text">
          <h1 className="post-hero__title">{title}</h1>

          <div className="post-hero__meta">
            <time dateTime={publishedAt}>{formattedDate}</time>
            <span>{readingTime} dk okuma</span>
          </div>

          {ctaUrl && (
            <a
              href={ctaUrl}
              className="post-hero__cta"
              target="_blank"
              rel="noopener noreferrer nofollow"
            >
              Hemen Giriş Yap <span className="post-hero__cta-arrow">&rarr;</span>
            </a>
          )}
        </div>
      </div>
    </section>
  );
}
