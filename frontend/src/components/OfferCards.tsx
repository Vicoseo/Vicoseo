import { TopOffer } from '@/types';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';
const BACKEND_URL = API_URL.replace(/\/api\/v1$/, '');

function resolveLogoUrl(url: string): string {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `${BACKEND_URL}${url}`;
}

interface OfferCardsProps {
  offers: TopOffer[];
  contactUrl?: string | null;
  contactText?: string | null;
}

export default function OfferCards({ offers, contactUrl, contactText }: OfferCardsProps) {
  const active = offers.filter((o) => o.is_active !== false);
  if (active.length === 0) return null;

  return (
    <section className="offer-cards-section">
      <div className="offer-cards-container">
        <div className="offer-cards-grid">
          {active.map((offer) => (
            <a
              key={offer.id}
              href={offer.slug ? `/go/${offer.slug}` : offer.target_url}
              target="_blank"
              rel="noopener noreferrer"
              className="offer-grid-card"
            >
              <div className="offer-grid-card__logo">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={resolveLogoUrl(offer.logo_url)}
                  alt={offer.bonus_text}
                  width={56}
                  height={56}
                />
              </div>
              <div className="offer-grid-card__content">
                <span className="offer-grid-card__bonus">{offer.bonus_text}</span>
              </div>
              <span className="offer-grid-card__cta">{offer.cta_text || 'ÜYE OL'}</span>
            </a>
          ))}
        </div>

        {contactText && contactUrl && (
          <div className="offer-cards-contact">
            <span>{contactText}: </span>
            <a href={contactUrl} target="_blank" rel="noopener noreferrer">
              Tıklayın
            </a>
          </div>
        )}
      </div>
    </section>
  );
}
