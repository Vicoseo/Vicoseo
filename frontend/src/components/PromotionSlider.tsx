'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import type { SitePromotion } from '@/types';

interface Props {
  promotions: SitePromotion[];
  domain: string;
  loginUrl?: string;
}

function resolveUrl(url: string, domain: string) {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `https://${domain}${url}`;
}

export default function PromotionSlider({ promotions, domain, loginUrl }: Props) {
  const [current, setCurrent] = useState(0);
  const touchStartX = useRef(0);
  const touchEndX = useRef(0);

  const next = useCallback(() => {
    setCurrent((c) => (c + 1) % promotions.length);
  }, [promotions.length]);

  const prev = useCallback(() => {
    setCurrent((c) => (c - 1 + promotions.length) % promotions.length);
  }, [promotions.length]);

  useEffect(() => {
    if (promotions.length <= 1) return;
    const timer = setInterval(next, 4000);
    return () => clearInterval(timer);
  }, [next, promotions.length]);

  if (!promotions || promotions.length === 0) return null;

  const item = promotions[current];
  const href = item.link_url || null;

  const handleTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX;
  };

  const handleTouchEnd = (e: React.TouchEvent) => {
    touchEndX.current = e.changedTouches[0].clientX;
    const diff = touchStartX.current - touchEndX.current;
    if (Math.abs(diff) > 50) {
      if (diff > 0) next();
      else prev();
    }
  };

  // Show max 8 dots, use range around current
  const maxDots = 8;
  const showDots = promotions.length <= maxDots;
  let dotStart = 0;
  let dotEnd = promotions.length;
  if (!showDots) {
    dotStart = Math.max(0, current - Math.floor(maxDots / 2));
    dotEnd = dotStart + maxDots;
    if (dotEnd > promotions.length) {
      dotEnd = promotions.length;
      dotStart = Math.max(0, dotEnd - maxDots);
    }
  }

  return (
    <div
      style={{
        position: 'relative',
        maxWidth: 1200,
        margin: '0 auto',
        borderRadius: '10px',
        overflow: 'hidden',
      }}
      onTouchStart={handleTouchStart}
      onTouchEnd={handleTouchEnd}
    >
      {href ? (
        <a
          href={href}
          target="_blank"
          rel="noopener noreferrer nofollow"
          style={{ display: 'block' }}
        >
          <img
            src={resolveUrl(item.image, domain)}
            alt={item.title || 'Promosyon'}
            style={{
              width: '100%',
              display: 'block',
              aspectRatio: '25/8',
              objectFit: 'cover',
            }}
          />
        </a>
      ) : (
        <img
          src={resolveUrl(item.image, domain)}
          alt={item.title || 'Promosyon'}
          style={{
            width: '100%',
            display: 'block',
            aspectRatio: '25/8',
            objectFit: 'cover',
          }}
        />
      )}

      {promotions.length > 1 && (
        <>
          <button
            onClick={(e) => { e.preventDefault(); prev(); }}
            aria-label="Önceki"
            className="promo-slider-arrow promo-slider-arrow--left"
          >
            &#8249;
          </button>
          <button
            onClick={(e) => { e.preventDefault(); next(); }}
            aria-label="Sonraki"
            className="promo-slider-arrow promo-slider-arrow--right"
          >
            &#8250;
          </button>

          <div style={{
            position: 'absolute',
            bottom: 6,
            left: '50%',
            transform: 'translateX(-50%)',
            display: 'flex',
            gap: 4,
            alignItems: 'center',
          }}>
            {dotStart > 0 && (
              <span style={{ width: 4, height: 4, borderRadius: '50%', background: 'rgba(255,255,255,0.3)' }} />
            )}
            {promotions.slice(dotStart, dotEnd).map((_, idx) => {
              const i = dotStart + idx;
              return (
                <button
                  key={i}
                  onClick={() => setCurrent(i)}
                  aria-label={`Slide ${i + 1}`}
                  style={{
                    width: i === current ? 16 : 6,
                    height: 6,
                    borderRadius: 3,
                    border: 'none',
                    background: i === current ? '#f59e0b' : 'rgba(255,255,255,0.5)',
                    cursor: 'pointer',
                    padding: 0,
                    transition: 'all 0.2s',
                  }}
                />
              );
            })}
            {dotEnd < promotions.length && (
              <span style={{ width: 4, height: 4, borderRadius: '50%', background: 'rgba(255,255,255,0.3)' }} />
            )}
          </div>
        </>
      )}
    </div>
  );
}
