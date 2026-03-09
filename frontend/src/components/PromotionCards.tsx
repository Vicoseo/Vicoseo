'use client';

import type { SitePromotion } from '@/types';

interface Props {
  cards: SitePromotion[];
  domain: string;
  loginUrl?: string;
}

function resolveUrl(url: string, domain: string) {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `https://${domain}${url}`;
}

export default function PromotionCards({ cards, domain, loginUrl }: Props) {
  if (!cards || cards.length === 0) return null;

  return (
    <div className="promo-cards">
      {cards.map((card) => {
        const href = card.link_url || loginUrl || null;
        const imgEl = (
          <figure className="promo-cards__figure">
            <img
              src={resolveUrl(card.image, domain)}
              alt={card.title || 'Promosyon'}
              title={card.title || undefined}
              loading="lazy"
              className="promo-cards__img"
            />
            {card.title && (
              <figcaption className="promo-cards__caption">
                {card.title}
              </figcaption>
            )}
          </figure>
        );
        return href ? (
          <a
            key={card.id}
            href={href}
            target="_blank"
            rel="noopener noreferrer nofollow"
            className="promo-cards__item"
          >
            {imgEl}
          </a>
        ) : (
          <div key={card.id} className="promo-cards__item">
            {imgEl}
          </div>
        );
      })}
    </div>
  );
}
