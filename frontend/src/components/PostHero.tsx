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
      <Image
        src={imageUrl}
        alt={title}
        width={800}
        height={445}
        className="post-hero__bg"
        priority
        sizes="100vw"
      />
      <div className="post-hero__overlay" />
      <div className="post-hero__inner">
        <h1 className="post-hero__title">{title}</h1>
      </div>
    </section>
  );
}
