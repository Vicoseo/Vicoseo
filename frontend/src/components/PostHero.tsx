import Image from 'next/image';
import type { HeroData } from '@/types';

interface PostHeroProps {
  title: string;
  hero: HeroData;
  siteUrl: string;
  logoUrl?: string;
}

export default function PostHero({
  title,
  hero,
  siteUrl,
  logoUrl,
}: PostHeroProps) {
  if (!hero.background) return null;

  return (
    <section className="post-hero">
      <Image
        src={hero.background.image_url.startsWith('/') ? `${siteUrl}${hero.background.image_url}` : hero.background.image_url}
        alt=""
        fill
        className="post-hero__bg"
        priority
        sizes="100vw"
      />
      <div className="post-hero__overlay" />
      <div className="post-hero__inner">
        {logoUrl && (
          <img
            src={logoUrl}
            alt=""
            className="post-hero__logo"
            aria-hidden="true"
          />
        )}
        <h1 className="post-hero__title">{title}</h1>
      </div>
    </section>
  );
}
