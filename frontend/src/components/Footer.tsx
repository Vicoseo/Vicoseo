import Link from 'next/link';
import { SiteConfig, SocialLinks, Page } from '@/types';

interface FooterProps {
  site: SiteConfig;
  pages?: Page[];
}

const PAGE_LABELS: Record<string, string> = {
  'hakkimizda': 'Hakkımızda',
  'iletisim': 'İletişim',
  'gizlilik-politikasi': 'Gizlilik Politikası',
  'sorumluluk-reddi': 'Sorumluluk Reddi',
  'sartlar-ve-kosullar': 'Şartlar ve Koşullar',
  'promosyonlar': 'Promosyonlar',
  'cerez-politikasi': 'Çerez Politikası',
};

const SOCIAL_LABELS: Record<keyof SocialLinks, string> = {
  telegram: 'Telegram',
  instagram: 'Instagram',
  x: 'X',
  youtube: 'YouTube',
  tiktok: 'TikTok',
  whatsapp: 'WhatsApp',
  support_email: 'E-posta',
};

export default function Footer({ site, pages = [] }: FooterProps) {
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
                  rel="noopener noreferrer nofollow"
                  className="site-footer__social-link"
                >
                  {label}
                </a>
              );
            })}
          </nav>
        )}

        {pages.length > 0 && (
          <nav className="site-footer__pages" aria-label="Site sayfaları">
            <Link href="/" className="site-footer__link">Ana Sayfa</Link>
            {pages.map((page) => (
              <Link
                key={page.slug}
                href={`/${page.slug}`}
                className="site-footer__link"
              >
                {PAGE_LABELS[page.slug] || page.title}
              </Link>
            ))}
            <Link href="/blog" className="site-footer__link">Blog</Link>
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
                    rel="noopener noreferrer nofollow"
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
