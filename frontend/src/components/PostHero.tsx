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
    <figure className="post-hero">
      <img
        src={imageUrl}
        alt={title}
        title={title}
        className="post-hero__bg"
        loading="eager"
      />
      <figcaption className="post-hero__caption">
        <span className="post-hero__title">{title}</span>
      </figcaption>
    </figure>
  );
}
