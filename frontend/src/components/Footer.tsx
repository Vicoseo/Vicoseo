import Link from 'next/link';
import { SiteConfig, SocialLinks } from '@/types';

interface FooterProps {
  site: SiteConfig;
}

const SOCIAL_LABELS: Record<keyof SocialLinks, string> = {
  telegram: 'Telegram',
  instagram: 'Instagram',
  x: 'X',
  youtube: 'YouTube',
  tiktok: 'TikTok',
  whatsapp: 'WhatsApp',
  support_email: 'E-posta',
};

export default function Footer({ site }: FooterProps) {
  const currentYear = new Date().getFullYear();
  const footerLinks = site.footer_links || [];
  const socialLinks = site.social_links || {};

  const activeSocials = Object.entries(socialLinks).filter(
    ([, url]) => url && url.trim().length > 0
  ) as [keyof SocialLinks, string][];

  return (
    <footer className="site-footer">
      <div className="site-footer__inner">
        {activeSocials.length > 0 && (
          <nav className="site-footer__social" aria-label="Sosyal medya">
            {activeSocials.map(([key, url]) => {
              const label = SOCIAL_LABELS[key] || key;
              if (key === 'support_email') {
                return (
                  <a
                    key={key}
                    href={`mailto:${url}`}
                    className="site-footer__social-link"
                  >
                    {label}
                  </a>
                );
              }
              return (
                <a
                  key={key}
                  href={url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="site-footer__social-link"
                >
                  {label}
                </a>
              );
            })}
          </nav>
        )}

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

        <p className="site-footer__copy">
          &copy; {currentYear} {site.name}. Tüm hakları saklıdır.
        </p>
      </div>
    </footer>
  );
}
