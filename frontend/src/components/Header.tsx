'use client';

import { useState } from 'react';
import { SiteConfig, Page } from '@/types';
import Link from 'next/link';
import Image from 'next/image';

interface HeaderProps {
  site: SiteConfig;
  pages?: Page[];
}

const NAV_LABELS: Record<string, string> = {
  'hakkimizda': 'Hakkımızda',
  'iletisim': 'İletişim',
  'gizlilik-politikasi': 'Gizlilik',
  'sorumluluk-reddi': 'Sorumluluk Reddi',
  'sartlar-ve-kosullar': 'Şartlar',
  'promosyonlar': 'Promosyonlar',
  'cerez-politikasi': 'Çerez Politikası',
};

function isColorDark(hex: string): boolean {
  const c = hex.replace('#', '');
  const r = parseInt(c.substring(0, 2), 16);
  const g = parseInt(c.substring(2, 4), 16);
  const b = parseInt(c.substring(4, 6), 16);
  return (r * 299 + g * 587 + b * 114) / 1000 < 128;
}

export default function Header({ site, pages = [] }: HeaderProps) {
  const [menuOpen, setMenuOpen] = useState(false);
  const headerBg = site.header_bg_color || site.primary_color || '#1a1a2e';
  const isDark = isColorDark(headerBg);
  const textColor = isDark ? '#fff' : '#555';
  const hamburgerColor = isDark ? '#fff' : '#333';

  return (
    <header
      style={{
        background: headerBg,
        borderBottom: `3px solid ${site.primary_color}`,
        padding: '0 16px',
        position: 'relative',
      }}
    >
      <div
        style={{
          maxWidth: 1200,
          margin: '0 auto',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          minHeight: 52,
          padding: '6px 0',
        }}
      >
        <Link href="/" style={{ textDecoration: 'none', flexShrink: 0, display: 'flex', alignItems: 'center' }}>
          {site.logo_url ? (
            <Image
              src={site.logo_url.startsWith('/') ? `https://${site.domain}${site.logo_url}` : site.logo_url}
              alt={site.name}
              width={120}
              height={34}
              style={{ objectFit: 'contain', maxWidth: '120px', height: 'auto' }}
              sizes="(max-width: 480px) 100px, 120px"
              priority
            />
          ) : (
            <span
              style={{
                fontSize: 18,
                fontWeight: 700,
                color: site.primary_color,
              }}
            >
              {site.name}
            </span>
          )}
        </Link>

        {/* Desktop nav */}
        <nav className="header-nav-desktop" aria-label="Ana menü">
          <Link href="/" className="header-nav-link" style={isDark ? { color: textColor } : undefined}>Ana Sayfa</Link>
          {pages.slice(0, 3).map((page) => (
            <Link key={page.slug} href={`/${page.slug}`} className="header-nav-link" style={isDark ? { color: textColor } : undefined}>
              {NAV_LABELS[page.slug] || page.title}
            </Link>
          ))}
          <Link href="/blog" className="header-nav-link" style={isDark ? { color: textColor } : undefined}>Blog</Link>
        </nav>

        {/* Mobile hamburger button */}
        <button
          className="header-hamburger"
          onClick={() => setMenuOpen(!menuOpen)}
          aria-label="Menü"
          aria-expanded={menuOpen}
        >
          <span style={{
            display: 'block',
            width: 20,
            height: 2,
            background: hamburgerColor,
            transition: 'all 0.2s',
            transform: menuOpen ? 'rotate(45deg) translate(4px, 4px)' : 'none',
          }} />
          <span style={{
            display: 'block',
            width: 20,
            height: 2,
            background: hamburgerColor,
            transition: 'all 0.2s',
            opacity: menuOpen ? 0 : 1,
            marginTop: 5,
          }} />
          <span style={{
            display: 'block',
            width: 20,
            height: 2,
            background: hamburgerColor,
            transition: 'all 0.2s',
            transform: menuOpen ? 'rotate(-45deg) translate(4px, -4px)' : 'none',
            marginTop: menuOpen ? 0 : 5,
          }} />
        </button>
      </div>

      {/* Mobile dropdown */}
      {menuOpen && (
        <nav
          className="header-nav-mobile"
          aria-label="Mobil menü"
          onClick={() => setMenuOpen(false)}
          style={isDark ? { background: headerBg } : undefined}
        >
          <Link href="/" className="header-nav-mobile-link" style={isDark ? { color: textColor } : undefined}>Ana Sayfa</Link>
          {pages.slice(0, 5).map((page) => (
            <Link key={page.slug} href={`/${page.slug}`} className="header-nav-mobile-link" style={isDark ? { color: textColor } : undefined}>
              {NAV_LABELS[page.slug] || page.title}
            </Link>
          ))}
          <Link href="/blog" className="header-nav-mobile-link" style={isDark ? { color: textColor } : undefined}>Blog</Link>
        </nav>
      )}
    </header>
  );
}
