import Link from 'next/link';
import { SiteConfig } from '@/types';

interface FooterProps {
  site: SiteConfig;
}

export default function Footer({ site }: FooterProps) {
  const currentYear = new Date().getFullYear();
  const footerLinks = site.footer_links || [];

  return (
    <footer className="site-footer">
      <div className="site-footer__inner">
        <p className="site-footer__copy">
          &copy; {currentYear} {site.name}. Tüm hakları saklıdır.
        </p>

        {footerLinks.length > 0 && (
          <nav className="site-footer__nav" aria-label="Footer navigasyonu">
            {footerLinks.map((link) => {
              const isExternal =
                link.link_url.startsWith('http://') ||
                link.link_url.startsWith('https://');

              if (isExternal) {
                return (
                  <a
                    key={link.id}
                    href={link.link_url}
                    rel="noopener noreferrer"
                    target="_blank"
                    className="site-footer__link"
                  >
                    {link.link_text}
                  </a>
                );
              }

              return (
                <Link
                  key={link.id}
                  href={link.link_url}
                  className="site-footer__link"
                >
                  {link.link_text}
                </Link>
              );
            })}
          </nav>
        )}
      </div>
    </footer>
  );
}
