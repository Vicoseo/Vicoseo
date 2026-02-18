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

export default function Header({ site, pages = [] }: HeaderProps) {
  return (
    <header
      style={{
        background: '#fff',
        borderBottom: `3px solid ${site.primary_color}`,
        padding: '0 16px',
      }}
    >
      <div
        style={{
          maxWidth: 1200,
          margin: '0 auto',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          height: 64,
        }}
      >
        <Link href="/" style={{ textDecoration: 'none' }}>
          {site.logo_url ? (
            <Image
              src={site.logo_url}
              alt={site.name}
              width={150}
              height={40}
              style={{ objectFit: 'contain' }}
              sizes="150px"
              priority
            />
          ) : (
            <span
              style={{
                fontSize: 22,
                fontWeight: 700,
                color: site.primary_color,
              }}
            >
              {site.name}
            </span>
          )}
        </Link>
        <nav style={{ display: 'flex', gap: 20, flexWrap: 'wrap' }} aria-label="Ana menü">
          <Link
            href="/"
            style={{
              color: '#555',
              textDecoration: 'none',
              fontWeight: 500,
              fontSize: 14,
            }}
          >
            Ana Sayfa
          </Link>
          {pages.slice(0, 3).map((page) => (
            <Link
              key={page.slug}
              href={`/${page.slug}`}
              style={{
                color: '#555',
                textDecoration: 'none',
                fontWeight: 500,
                fontSize: 14,
              }}
            >
              {NAV_LABELS[page.slug] || page.title}
            </Link>
          ))}
          <Link
            href="/blog"
            style={{
              color: '#555',
              textDecoration: 'none',
              fontWeight: 500,
              fontSize: 14,
            }}
          >
            Blog
          </Link>
        </nav>
      </div>
    </header>
  );
}
