import Image from 'next/image';
import type { HeroData } from '@/types';

interface PostHeroProps {
  title: string;
  hero: HeroData;
  siteUrl: string;
}

export default function PostHero({
  title,
  hero,
  siteUrl,
}: PostHeroProps) {
  if (!hero.background) return null;

  const imageUrl = hero.background.image_url.startsWith('/')
    ? `${siteUrl}${hero.background.image_url}`
    : hero.background.image_url;

  return (
    <section className="post-hero">
      <img
        src={imageUrl}
        alt={title}
        className="post-hero__bg"
        loading="eager"
      />
      <div className="post-hero__inner">
        <h1 className="post-hero__title">{title}</h1>
      </div>
    </section>
  );
}
