'use client';

import { TopOffer } from '@/types';
import { useRef, useEffect, useCallback, useState } from 'react';

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
  const [isPaused, setIsPaused] = useState(false);
  const scrollRef = useRef<HTMLDivElement>(null);
  const animationRef = useRef<number | null>(null);
  const scrollPosRef = useRef(0);

  // Triple items for seamless infinite loop
  const displayItems = active.length > 0 ? [...active, ...active, ...active] : [];

  const animate = useCallback(() => {
    if (!scrollRef.current || isPaused) {
      animationRef.current = requestAnimationFrame(animate);
      return;
    }

    scrollPosRef.current += 0.6;
    const oneThird = scrollRef.current.scrollWidth / 3;

    if (scrollPosRef.current >= oneThird) {
      scrollPosRef.current = 0;
    }

    scrollRef.current.style.transform = `translateX(-${scrollPosRef.current}px)`;
    animationRef.current = requestAnimationFrame(animate);
  }, [isPaused]);

  useEffect(() => {
    if (active.length >= 1) {
      animationRef.current = requestAnimationFrame(animate);
      return () => {
        if (animationRef.current) {
          cancelAnimationFrame(animationRef.current);
        }
      };
    }
  }, [animate, active.length]);

  if (active.length === 0) return null;

  return (
    <section className="top-offers-section">
      <div
        className="top-offers-marquee"
        onMouseEnter={() => setIsPaused(true)}
        onMouseLeave={() => setIsPaused(false)}
      >
        <div
          ref={scrollRef}
          className="top-offers-track"
          style={{ display: 'flex', gap: 12, willChange: 'transform' }}
        >
          {displayItems.map((offer, idx) => (
            <a
              key={`${offer.id}-${idx}`}
              href={offer.slug ? `/go/${offer.slug}` : offer.target_url}
              className="offer-card"
              target="_blank"
              rel="noopener noreferrer"
            >
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={resolveLogoUrl(offer.logo_url)}
                alt={`${offer.bonus_text} - ${offer.cta_text || 'Üye Ol'}`}
                width={44}
                height={44}
              />
              <span className="offer-card__text">{offer.bonus_text}</span>
              <span className="offer-card__cta">{offer.cta_text || 'ÜYE OL'}</span>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}
