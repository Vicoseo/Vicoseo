'use client';

import { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { SiteConfig, Page, Post } from '@/types';
import ParavanBlogSearch from './ParavanBlogSearch';

const NAV_LABELS: Record<string, string> = {
  'hakkimizda': 'Hakkımızda',
  'iletisim': 'İletişim',
  'gizlilik-politikasi': 'Gizlilik',
  'sorumluluk-reddi': 'Sorumluluk Reddi',
  'sartlar-ve-kosullar': 'Şartlar',
  'promosyonlar': 'Promosyonlar',
  'cerez-politikasi': 'Çerez Politikası',
};

interface ParavanBlogHeaderProps {
  site: SiteConfig;
  pages: Page[];
  posts: Post[];
  loginUrl: string;
}

export default function ParavanBlogHeader({ site, pages, posts, loginUrl }: ParavanBlogHeaderProps) {
  const [menuOpen, setMenuOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);

  return (
    <>
      <header className="pb-header">
        <div className="pb-header__inner">
          {/* Logo */}
          <Link href="/" className="pb-header__logo">
            {site.logo_url ? (
              <Image
                src={site.logo_url.startsWith('/') ? `https://${site.domain}${site.logo_url}` : site.logo_url}
                alt={site.name}
                width={130}
                height={36}
                style={{ objectFit: 'contain', maxWidth: '130px', height: 'auto' }}
                sizes="130px"
                priority
              />
            ) : (
              <span className="pb-header__brand">{site.name}</span>
            )}
          </Link>

          {/* Desktop nav */}
          <nav className="pb-header__nav" aria-label="Ana menü">
            <Link href="/">Ana Sayfa</Link>
            {pages.slice(0, 4).map((page) => (
              <Link key={page.slug} href={`/${page.slug}`}>
                {NAV_LABELS[page.slug] || page.title}
              </Link>
            ))}
            <Link href="/blog">Blog</Link>
          </nav>

          {/* Right actions */}
          <div className="pb-header__actions">
            <button
              className="pb-header__search-btn"
              onClick={() => setSearchOpen(true)}
              aria-label="Ara"
              type="button"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </button>

            <a href={loginUrl} className="pb-header__cta" rel="nofollow noopener" target="_blank">
              Giriş Yap
            </a>

            {/* Mobile hamburger */}
            <button
              className="pb-header__hamburger"
              onClick={() => setMenuOpen(!menuOpen)}
              aria-label="Menü"
              aria-expanded={menuOpen}
              type="button"
            >
              <span className={`pb-header__hamburger-line ${menuOpen ? 'pb-header__hamburger-line--open1' : ''}`} />
              <span className={`pb-header__hamburger-line ${menuOpen ? 'pb-header__hamburger-line--open2' : ''}`} />
              <span className={`pb-header__hamburger-line ${menuOpen ? 'pb-header__hamburger-line--open3' : ''}`} />
            </button>
          </div>
        </div>

        {/* Mobile dropdown */}
        {menuOpen && (
          <nav className="pb-header__mobile" aria-label="Mobil menü" onClick={() => setMenuOpen(false)}>
            <Link href="/">Ana Sayfa</Link>
            {pages.slice(0, 5).map((page) => (
              <Link key={page.slug} href={`/${page.slug}`}>
                {NAV_LABELS[page.slug] || page.title}
              </Link>
            ))}
            <Link href="/blog">Blog</Link>
            <button
              className="pb-header__mobile-search"
              onClick={(e) => { e.stopPropagation(); setMenuOpen(false); setSearchOpen(true); }}
              type="button"
            >
              Ara...
            </button>
          </nav>
        )}
      </header>

      {searchOpen && (
        <ParavanBlogSearch posts={posts} onClose={() => setSearchOpen(false)} />
      )}
    </>
  );
}
