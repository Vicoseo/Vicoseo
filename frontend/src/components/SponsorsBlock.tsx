import { TopOffer } from '@/types';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';
const BACKEND_URL = API_URL.replace(/\/api\/v1$/, '');

function resolveLogoUrl(url: string): string {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `${BACKEND_URL}${url}`;
}

interface SponsorsBlockProps {
  offers: TopOffer[];
}

export default function SponsorsBlock({ offers }: SponsorsBlockProps) {
  const active = offers.filter((o) => o.is_active !== false);
  if (active.length === 0) return null;

  return (
    <section className="top-offers-section">
      <div className="top-offers-grid">
        {active.map((offer) => (
          <a
            key={`${offer.source}-${offer.id}`}
            href={offer.slug ? `/go/${offer.slug}` : offer.target_url}
            className="offer-card"
          >
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={resolveLogoUrl(offer.logo_url)}
              alt=""
              width={44}
              height={44}
            />
            <span className="offer-card__text">{offer.bonus_text}</span>
            <span className="offer-card__cta">{offer.cta_text || 'ÜYE OL'}</span>
          </a>
        ))}
      </div>
    </section>
  );
}
