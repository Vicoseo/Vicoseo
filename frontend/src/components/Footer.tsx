import { SiteConfig } from '@/types';

interface FooterProps {
  site: SiteConfig;
}

export default function Footer({ site }: FooterProps) {
  const currentYear = new Date().getFullYear();
  const footerLinks = site.footer_links || [];

  return (
    <footer
      style={{
        background: '#1a1a2e',
        color: '#ccc',
        padding: '32px 16px',
        marginTop: 'auto',
      }}
    >
      <div
        style={{
          maxWidth: 1200,
          margin: '0 auto',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          flexWrap: 'wrap',
          gap: 16,
        }}
      >
        <p style={{ fontSize: 14, margin: 0 }}>
          &copy; {currentYear} {site.name}. All rights reserved.
        </p>

        {footerLinks.length > 0 && (
          <nav
            style={{
              display: 'flex',
              flexWrap: 'wrap',
              gap: 16,
              alignItems: 'center',
            }}
          >
            {footerLinks.map((link) => (
              <a
                key={link.id}
                href={link.link_url}
                rel="nofollow"
                target="_blank"
                style={{
                  color: '#aaa',
                  fontSize: 13,
                  textDecoration: 'none',
                  transition: 'color 0.2s',
                }}
                onMouseEnter={(e) => (e.currentTarget.style.color = '#fff')}
                onMouseLeave={(e) => (e.currentTarget.style.color = '#aaa')}
              >
                {link.link_text}
              </a>
            ))}
          </nav>
        )}
      </div>
    </footer>
  );
}
