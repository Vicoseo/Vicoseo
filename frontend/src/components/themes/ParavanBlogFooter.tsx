import Link from 'next/link';
import { SiteConfig, Page, SocialLinks } from '@/types';

const PAGE_LABELS: Record<string, string> = {
  'hakkimizda': 'Hakkımızda',
  'iletisim': 'İletişim',
  'gizlilik-politikasi': 'Gizlilik Politikası',
  'sorumluluk-reddi': 'Sorumluluk Reddi',
  'sartlar-ve-kosullar': 'Şartlar ve Koşullar',
  'promosyonlar': 'Promosyonlar',
  'cerez-politikasi': 'Çerez Politikası',
};

const SOCIAL_ICONS: Record<string, string> = {
  telegram: 'Telegram',
  instagram: 'Instagram',
  x: 'Twitter/X',
  youtube: 'YouTube',
  tiktok: 'TikTok',
  whatsapp: 'WhatsApp',
  support_email: 'E-posta',
};

interface ParavanBlogFooterProps {
  site: SiteConfig;
  pages: Page[];
  loginUrl: string;
}

export default function ParavanBlogFooter({ site, pages, loginUrl }: ParavanBlogFooterProps) {
  const brandName = site.name;
  const socialLinks = site.social_links || {};
  const footerLinks = site.footer_links || [];

  const activeSocials = Object.entries(socialLinks).filter(
    ([, url]) => url && url.trim().length > 0
  ) as [keyof SocialLinks, string][];

  return (
    <footer className="pb-footer">
      <div className="pb-footer__inner">
        <div className="pb-footer__top">
          <div className="pb-footer__brand">
            <h4>{brandName}</h4>
            <p>
              {brandName}, platform hakkında güncel bilgiler, giriş adresleri ve detaylı incelemeler sunan bağımsız bir rehber sitesidir. 18 yaşından küçüklerin bahis oynaması yasaktır.
            </p>
          </div>

          <div className="pb-footer__col">
            <h4>Sayfalar</h4>
            <Link href="/">Ana Sayfa</Link>
            {pages.map((page) => (
              <Link key={page.slug} href={`/${page.slug}`}>
                {PAGE_LABELS[page.slug] || page.title}
              </Link>
            ))}
            <Link href="/blog">Blog</Link>
          </div>

          <div className="pb-footer__col">
            <h4>Oyunlar</h4>
            <a href={loginUrl} rel="nofollow">Slot Oyunları</a>
            <a href={loginUrl} rel="nofollow">Canlı Casino</a>
            <a href={loginUrl} rel="nofollow">Spor Bahisleri</a>
          </div>

          {activeSocials.length > 0 && (
            <div className="pb-footer__col">
              <h4>İletişim</h4>
              {activeSocials.map(([key, url]) => {
                const label = SOCIAL_ICONS[key] || key;
                if (key === 'support_email') {
                  return <a key={key} href={`mailto:${url}`}>{label}</a>;
                }
                return (
                  <a key={key} href={url} target="_blank" rel="noopener nofollow">
                    {label}
                  </a>
                );
              })}
            </div>
          )}
        </div>

        {footerLinks.length > 0 && (
          <div className="pb-footer__links">
            {footerLinks.map((link) => {
              const isExternal = link.link_url.startsWith('http://') || link.link_url.startsWith('https://');
              if (isExternal) {
                return (
                  <a key={link.id} href={link.link_url} target="_blank" rel="noopener noreferrer nofollow">
                    {link.link_text}
                  </a>
                );
              }
              return (
                <Link key={link.id} href={link.link_url}>
                  {link.link_text}
                </Link>
              );
            })}
          </div>
        )}

        <div className="pb-footer__bottom">
          &copy; {new Date().getFullYear()} {brandName}. Tüm hakları saklıdır.<br />
          Bu site yalnızca bilgilendirme amaçlıdır. Kumar bağımlılık yapabilir, sorumlu oynayınız.
        </div>
      </div>
    </footer>
  );
}
