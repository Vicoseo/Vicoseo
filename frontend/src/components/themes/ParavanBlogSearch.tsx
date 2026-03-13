'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import Link from 'next/link';
import { Post } from '@/types';

interface ParavanBlogSearchProps {
  posts: Post[];
  onClose: () => void;
}

export default function ParavanBlogSearch({ posts, onClose }: ParavanBlogSearchProps) {
  const [query, setQuery] = useState('');
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    inputRef.current?.focus();
  }, []);

  const handleKeyDown = useCallback((e: KeyboardEvent) => {
    if (e.key === 'Escape') onClose();
  }, [onClose]);

  useEffect(() => {
    document.addEventListener('keydown', handleKeyDown);
    document.body.style.overflow = 'hidden';
    return () => {
      document.removeEventListener('keydown', handleKeyDown);
      document.body.style.overflow = '';
    };
  }, [handleKeyDown]);

  const q = query.toLowerCase().trim();
  const results = q.length >= 2
    ? posts.filter(
        (p) =>
          p.title.toLowerCase().includes(q) ||
          (p.excerpt && p.excerpt.toLowerCase().includes(q))
      ).slice(0, 10)
    : [];

  return (
    <div className="pb-search-overlay" onClick={onClose}>
      <div className="pb-search-modal" onClick={(e) => e.stopPropagation()}>
        <div className="pb-search-modal__header">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input
            ref={inputRef}
            type="text"
            className="pb-search-modal__input"
            placeholder="Site içinde ara..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
          />
          <button className="pb-search-modal__close" onClick={onClose} type="button" aria-label="Kapat">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <div className="pb-search-modal__results">
          {q.length >= 2 && results.length === 0 && (
            <p className="pb-search-modal__empty">Sonuç bulunamadı</p>
          )}
          {results.map((post) => (
            <Link
              key={post.id}
              href={`/blog/${post.slug}`}
              className="pb-search-modal__item"
              onClick={onClose}
            >
              <h4>{post.title}</h4>
              {post.excerpt && (
                <p>{post.excerpt.length > 120 ? post.excerpt.substring(0, 120) + '...' : post.excerpt}</p>
              )}
            </Link>
          ))}
          {q.length < 2 && q.length > 0 && (
            <p className="pb-search-modal__hint">En az 2 karakter girin</p>
          )}
        </div>
      </div>
    </div>
  );
}
