'use client';

import { Sponsor } from '@/types';
import Image from 'next/image';
import { useState, useRef, useEffect, useCallback } from 'react';

interface SponsorsProps {
  sponsors: Sponsor[];
  contactUrl?: string | null;
  contactText?: string | null;
}

const CATEGORIES = [
  { key: null, label: 'Tümü' },
  { key: 'vip', label: 'VIP Siteler' },
  { key: 'popular', label: 'En Çok Tercih Edilen' },
  { key: 'kutu', label: 'Kutu Açılışı' },
] as const;

export default function Sponsors({ sponsors, contactUrl, contactText }: SponsorsProps) {
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const [isPaused, setIsPaused] = useState(false);
  const scrollRef = useRef<HTMLDivElement>(null);
  const animationRef = useRef<number | null>(null);
  const scrollPosRef = useRef(0);

  const filtered = activeCategory
    ? sponsors.filter((s) => s.category === activeCategory)
    : sponsors;

  // Duplicate items for infinite scroll effect
  const displayItems = filtered.length > 0 ? [...filtered, ...filtered] : [];

  const animate = useCallback(() => {
    if (!scrollRef.current || isPaused) {
      animationRef.current = requestAnimationFrame(animate);
      return;
    }

    scrollPosRef.current += 0.5;
    const halfWidth = scrollRef.current.scrollWidth / 2;

    if (scrollPosRef.current >= halfWidth) {
      scrollPosRef.current = 0;
    }

    scrollRef.current.style.transform = `translateX(-${scrollPosRef.current}px)`;
    animationRef.current = requestAnimationFrame(animate);
  }, [isPaused]);

  useEffect(() => {
    if (filtered.length > 3) {
      animationRef.current = requestAnimationFrame(animate);
      return () => {
        if (animationRef.current) {
          cancelAnimationFrame(animationRef.current);
        }
      };
    }
  }, [animate, filtered.length]);

  const scrollLeft = () => {
    scrollPosRef.current = Math.max(0, scrollPosRef.current - 250);
  };

  const scrollRight = () => {
    scrollPosRef.current += 250;
  };

  if (!sponsors || sponsors.length === 0) return null;

  return (
    <section className="sponsors-section">
      <div className="sponsors-container">
        {/* Category Buttons */}
        <div className="sponsors-categories">
          {CATEGORIES.map((cat) => (
            <button
              key={cat.key ?? 'all'}
              className={`sponsors-cat-btn ${activeCategory === cat.key ? 'active' : ''}`}
              onClick={() => setActiveCategory(cat.key)}
            >
              {cat.label}
            </button>
          ))}
        </div>

        {/* Marquee/Slider */}
        {filtered.length > 0 ? (
          <div
            className="sponsors-slider-wrapper"
            onMouseEnter={() => setIsPaused(true)}
            onMouseLeave={() => setIsPaused(false)}
          >
            <button className="sponsors-arrow sponsors-arrow--left" onClick={scrollLeft} aria-label="Sola kaydır">
              &#8249;
            </button>

            <div className="sponsors-slider-viewport">
              <div
                ref={scrollRef}
                className="sponsors-slider-track"
                style={{ display: 'flex', gap: 12, willChange: 'transform' }}
              >
                {displayItems.map((sponsor, idx) => (
                  <a
                    key={`${sponsor.id}-${idx}`}
                    href={sponsor.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="sponsor-card"
                    style={{ borderLeftColor: sponsor.primary_color }}
                  >
                    {sponsor.logo && (
                      <div className="sponsor-card__logo">
                        <Image
                          src={sponsor.logo}
                          alt={sponsor.name}
                          width={48}
                          height={48}
                          style={{ objectFit: 'contain', borderRadius: 6 }}
                        />
                      </div>
                    )}
                    <div className="sponsor-card__content">
                      <span className="sponsor-card__name">{sponsor.name}</span>
                      {sponsor.promotions && sponsor.promotions.length > 0 && (
                        <span className="sponsor-card__promo">{sponsor.promotions[0].highlight}</span>
                      )}
                    </div>
                    <div className="sponsor-card__rating">
                      {'★'.repeat(sponsor.rating)}{'☆'.repeat(5 - sponsor.rating)}
                    </div>
                  </a>
                ))}
              </div>
            </div>

            <button className="sponsors-arrow sponsors-arrow--right" onClick={scrollRight} aria-label="Sağa kaydır">
              &#8250;
            </button>
          </div>
        ) : (
          <div className="sponsors-empty">Bu kategoride sponsor bulunmuyor</div>
        )}

        {/* Contact Section */}
        {contactText && contactUrl && (
          <div className="sponsors-contact">
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
