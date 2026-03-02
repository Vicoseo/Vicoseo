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
    <div style={{
      display: 'grid',
      gridTemplateColumns: 'repeat(3, 1fr)',
      gap: '10px',
      maxWidth: 1200,
      margin: '0 auto',
    }} className="promo-cards-grid">
      {cards.map((card) => {
        const href = card.link_url || loginUrl || '#';
        return (
          <a
            key={card.id}
            href={href}
            target="_blank"
            rel="noopener noreferrer nofollow"
            style={{
              display: 'block',
              borderRadius: '10px',
              overflow: 'hidden',
              transition: 'transform 0.2s, box-shadow 0.2s',
            }}
            className="promo-card-item"
          >
            <img
              src={resolveUrl(card.image, domain)}
              alt={card.title || 'Promosyon'}
              loading="lazy"
              style={{
                width: '100%',
                height: '100%',
                display: 'block',
                aspectRatio: '5/3',
                objectFit: 'cover',
              }}
            />
          </a>
        );
      })}
    </div>
  );
}
