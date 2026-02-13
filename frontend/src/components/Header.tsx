import { SiteConfig } from '@/types';
import Image from 'next/image';
import Link from 'next/link';

interface HeaderProps {
  site: SiteConfig;
}

export default function Header({ site }: HeaderProps) {
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
        <nav style={{ display: 'flex', gap: 24 }}>
          <Link
            href="/"
            style={{
              color: '#555',
              textDecoration: 'none',
              fontWeight: 500,
              fontSize: 15,
            }}
          >
            Home
          </Link>
          <Link
            href="/blog"
            style={{
              color: '#555',
              textDecoration: 'none',
              fontWeight: 500,
              fontSize: 15,
            }}
          >
            Blog
          </Link>
        </nav>
      </div>
    </header>
  );
}
