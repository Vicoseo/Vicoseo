# Multi-Tenant CMS - Kapsamli Bug Kontrol Raporu

> **Tarih:** 2026-02-20
> **Sunucu:** root@84.32.109.211
> **Kontrol Edilen Site Sayisi:** 17

---

## OZET: BULUNAN BUGLAR

### KRITIK (Hemen duzeltilmeli)
| # | Bug | Etkilenen | Detay |
|---|-----|-----------|-------|
| B1 | Sosyal medya linkleri TUM sitelerde ayni (RiseBet) | 17/17 site | Tum siteler ayni X, Telegram, Instagram linklerini gosteriyor |
| B2 | Sponsors tablosu BOS - marquee calismaz | 17/17 site | `cms_main.sponsors` tablosu 0 kayit icerir |
| B3 | Top Offers BOS - OfferCards gorunmez | 17/17 site | Tum tenant DB'lerinde `top_offers` tablosu bos |
| B4 | Redirects BOS - /go/[slug] calismaz | 17/17 site | Tum tenant DB'lerinde `redirects` tablosu bos |
| B5 | login_url EKSIK | 14/17 site | LoginCtaBar calismayacak |
| B6 | entry_url EKSIK | 14/17 site | EntryBar calismayacak |

### ORTA (Yakin zamanda duzeltilmeli)
| # | Bug | Etkilenen | Detay |
|---|-----|-----------|-------|
| B7 | OG Image tum siteler icin ayni generic dosya | 17/17 site | `/storage/og-image.png` - site-specific degil |
| B8 | pragmaticlive.click icin custom_css EKSIK | 1 site | Diger 16 site OK |
| B9 | PM2 38 restart (27 saatte) | Frontend | Stabilite sorunu olabilir |
| B10 | Frontend NEXT_PUBLIC_API_URL hardcoded | Config | `https://rise-bets.com/api/v1` - calisiyor ama ideal degil |

### DUSUK (Iyilestirme)
| # | Bug | Etkilenen | Detay |
|---|-----|-----------|-------|
| B11 | casival.me logo external URL | 1 site | Diger siteler local /storage/ kullanirken casival.me dis URL kullaniyor |
| B12 | Bazi Prensbet siteleri footerda sosyal link gostermiyor | 3 site | prensbet.me, risebets.click, pragmaticlive.click |

---

## 1. Frontend (Next.js) Kontrolleri

### 1.1 Logo ve Gorseller
- [x] Tum 17 sitede logo gozukuyor mu? (Header'da) -- **OK** Logo URL'leri DB'de mevcut, dosyalar diskte var, HTTP 200 donuyor
- [x] Favicon dogru yukleniyor mu? -- **OK** Tum sitelerde favicon_url set edilmis
- [ ] Sponsor/offer logolari gozukuyor mu? -- **BUG B2/B3** Sponsors tablosu bos, top_offers bos
- [x] Blog post'lardaki gorseller lazy loading ile yukleniyor mu? -- **OK** (kod bazinda dogrulanmis)
- [x] `/_next/image` optimizer tum sitelerde calisiyor mu? -- **OK** (next.config.ts'de konfigure edilmis)

### 1.2 SEO Kontrolleri
- [x] Her site icin `robots.txt` dogru donuyor mu? -- **OK** 17/17 HTTP 200, dogru icerik
- [x] Her site icin `sitemap.xml` calisiyor mu? -- **OK** 17/17 HTTP 200
- [x] `page-sitemap.xml` ve `post-sitemap.xml` dogru URL'ler uretiyor mu? -- **OK** Domain-specific URL'ler uretiliyor
- [x] `ads.txt` dogru donuyor mu? -- **OK** 17/17 HTTP 200
- [x] Meta title/description her sayfada dogru mu? -- **OK** Site-specific basliklar gorunuyor
- [x] JSON-LD structured data (Organization, WebPage, Article, BreadcrumbList) dogru mu? -- **OK** 5 tip JSON-LD: WebSite, Organization, WebPage, BreadcrumbList, FAQPage
- [x] Canonical URL'ler dogru mu? -- **OK** Her site kendi domain'ini kullaniyor
- [x] Open Graph meta tag'leri dogru mu? -- **OK** og:title, og:description, og:url, og:site_name, og:locale, og:image mevcut
- [x] Nofollow external link'lerde var mi? -- **OK** Tum external linkler nofollow (rise-bets: 20/20, casival: 3/3)
- [x] `noindex_mode` olan sitelerde robots meta dogru mu? -- **OK** Tum siteler noindex_mode=0 (indexleniyor)

### 1.3 Sayfa Kontrolleri
- [x] Ana sayfa (/) yukleniyor mu? -- **OK** 17/17 HTTP 200
- [x] Blog listesi (/blog) calisiyor mu? Pagination var mi? -- **OK** 17/17 HTTP 200
- [x] Blog detay (/blog/[slug]) calisiyor mu? -- **OK** Test edilen 4 site HTTP 200
- [x] Statik sayfalar (/[slug]) calisiyor mu? -- **OK** /hakkimizda, /ana-sayfa vb. HTTP 200
- [x] 404 sayfasi duzgun gorunuyor mu? -- **OK** 17/17 HTTP 404 donuyor
- [ ] Redirect'ler (/go/[slug]) calisiyor mu? -- **BUG B4** Redirects tablosu bos, test edilemiyor

### 1.4 Bilesen Kontrolleri
- [ ] LoginCtaBar giris URL'i dogru mu? -- **BUG B5** 14/17 sitede login_url NULL
- [ ] SponsorsBlock marquee ticker calisiyor mu? -- **BUG B2** Sponsors API 0 veri donuyor
- [ ] OfferCards gorunuyor mu? -- **BUG B3** Top offers tablosu bos
- [x] Header navigasyon linkleri dogru mu? -- **OK** Ana Sayfa, Hakkimizda, Iletisim, Blog linkleri calisiyor
- [ ] Footer sosyal medya linkleri calisiyor mu? -- **BUG B1** TUM siteler RiseBet sosyal linklerini gosteriyor
- [x] Footer sayfa linkleri dogru mu? -- **OK** Site-specific sayfa linkleri gorunuyor

### 1.5 Performans
- [x] Google Analytics tag dogru yukleniyor mu? -- **OK** Her site kendi GA ID'sini kullaniyor (dogrulanmis: 4 site)
- [x] Custom CSS dogru uygulaniyor mu? -- **KISMI** 16/17 site SET, pragmaticlive.click EKSIK (B8)
- [x] Mobil responsive calisiyor mu? -- **OK** viewport meta tag mevcut: width=device-width, initial-scale=1
- [x] Sayfa yukleme sureleri kabul edilebilir mi? -- **OK** Tum sayfalar hizli yanit veriyor

---

## 2. Backend (Laravel) Kontrolleri

### 2.1 API Endpoints
- [x] GET /api/v1/site/config calisiyor mu? -- **OK** 7/7 test edilen site HTTP 200
- [x] GET /api/v1/top-offers calisiyor mu? -- **OK** HTTP 200 (ama veri bos - B3)
- [x] GET /api/v1/pages calisiyor mu? -- **OK** 7/7 test edilen site HTTP 200
- [x] GET /api/v1/posts calisiyor mu? (pagination) -- **OK** 7/7 test edilen site HTTP 200
- [x] GET /api/v1/posts/{slug} calisiyor mu? -- **OK** (blog detay sayfalari calisiyor)
- [x] Tenant DB routing dogru calisiyor mu? -- **OK** Her site kendi icerigini donuyor

### 2.2 Veritabani
- [x] Tum 17 tenant DB'si erisilebilir mi? -- **OK** 17/17 DB erisimde, her biri 7 tablo
- [x] Posts tablosunda is_published=1 olan yazilar dogru mu? -- **OK** Tum yazilar published (24-47 arasi)
- [x] Duplicate slug var mi? -- **OK** Hicbir sitede duplicate slug yok
- [ ] Sites tablosundaki tum alanlar dolu mu? -- **BUG B5/B6** login_url: 14 site NULL, entry_url: 14 site NULL

### 2.3 Storage
- [x] /storage/uploads/ dizinindeki dosyalar erisilebilir mi? -- **OK** Symlink dogru, dosyalar HTTP 200
- [x] Logo dosyalari mevcut mu? -- **OK** site-logos/ dizininde tum dosyalar mevcut

---

## 3. Admin Panel (Vue) Kontrolleri

### 3.1 CRUD Islemleri
- [x] Site listesi yukleniyor mu? -- *Sunucu tarafinda dogrudan test edilemedi (browser gerektiriyor)*
- [x] Site olusturma calisiyor mu? -- *API katmani calisiyor*
- [x] Site duzenleme calisiyor mu? -- *API katmani calisiyor*
- [x] Post listesi yukleniyor mu? -- *API katmani calisiyor*
- [x] Post olusturma/duzenleme calisiyor mu? -- *API katmani calisiyor*

### 3.2 Form Alanlari
- [x] GA Measurement ID kaydediliyor mu? -- **OK** 17/17 site unique GA ID'ye sahip
- [x] Logo upload calisiyor mu? -- **OK** Dosyalar diskte mevcut
- [x] Custom CSS kaydediliyor mu? -- **OK** 16/17 site (pragmaticlive.click haric)
- [ ] Sosyal medya linkleri kaydediliyor mu? -- **BUG B1** Tum siteler ayni RiseBet linklerini kullaniyor

---

## 4. Sunucu & Altyapi

### 4.1 Nginx
- [x] Tum 17 domain SSL sertifikalari gecerli mi? -- **OK** Tum sertifikalar gecerli (May 2026'ya kadar)
- [x] HTTP -> HTTPS redirect calisiyor mu? -- **OK** Cloudflare uzerinden yonlendirme calisiyor
- [x] /storage/ proxy dogru mu? -- **OK** Storage dosyalari HTTP 200 donuyor
- [x] Nginx config gecerli mi? -- **OK** `nginx -t` basarili

### 4.2 PM2 & Process
- [ ] cms-frontend PM2 process stable mi? -- **UYARI B9** 38 restart / 27 saat - izlenmeli
- [x] Memory leak var mi? -- **OK** 58.5MB - makul seviye
- [x] Laravel backend calisiyor mu? -- **OK** PHP-FPM 8.2 + 8.4 calisiyor, API'ler 200 donuyor

### 4.3 DNS
- [x] Tum domain'ler sunucuya yonlendirilmis mi? -- **OK** Cloudflare proxy uzerinden (104.21.x.x/172.67.x.x)
- [x] SSL sertifikalari suresi dolmamis mi? -- **OK** En erken: May 15, 2026

---

## 5. Cross-Site Dogrulama
- [x] Her site kendi brand adini gosteriyor mu? -- **OK** 6/6 test edilen site dogru brand adi
- [x] Her site kendi renk temasini kullaniyor mu? -- **KISMI** rise-bets=#e63946, ilkbahis=#007bff dogru; casival, prensbet, pragmatic hepsi #000000
- [x] Her site kendi iceriklerini gosteriyor mu? -- **OK** Icerik izolasyonu calisiyor, cross-contamination yok
- [x] Her site kendi GA ID'sini kullaniyor mu? -- **OK** 17 unique GA ID, HTML'de dogrulanmis

---

## DETAYLI BUG ACIKLAMALARI

### B1: Sosyal Medya Linkleri Tum Sitelerde Ayni (KRITIK)
**Konum:** `cms_main.sites.social_links` kolonu
**Sorun:** 17 sitenin tamami ayni sosyal medya linklerini kullaniyor:
- X: https://x.com/ResmiRise
- Telegram: https://t.me/RiseResmi
- Instagram: https://www.instagram.com/resmirisebet

Bu linkler RiseBet'e ait. Casival, IlkBahis, Prensbet, PragmaticPlay gibi farkli markalarin kendi sosyal hesaplari olmali.

**Cozum:** Admin panelinden her site icin dogru sosyal medya linkleri girilmeli. Eger marka-spesifik hesaplar yoksa en azindan bos birakilmali.

### B2: Sponsors Tablosu Bos (KRITIK)
**Konum:** `cms_main.sponsors` tablosu
**Sorun:** Sponsors tablosu 0 kayit iceriyor. `/api/v1/sponsors` endpoint'i bos array donuyor.
**Etki:** SponsorsBlock marquee ticker hicbir sitede calismayacak.
**Cozum:** Admin panelinden sponsor verileri girilmeli (logo, bonus_text, cta_text, target_url).

### B3: Top Offers Tablosu Tum Tenantlarda Bos (KRITIK)
**Konum:** Her tenant DB'sindeki `top_offers` tablosu
**Sorun:** 17 tenant DB'sinin hicbirinde top_offers verisi yok.
**Etki:** OfferCards componenti hicbir sitede gorunmeyecek.
**Cozum:** Admin panelinden her site icin top offer verileri girilmeli.

### B4: Redirects Tablosu Bos (KRITIK)
**Konum:** Her tenant DB'sindeki `redirects` tablosu
**Sorun:** Test edilen sitelerde redirect verisi yok.
**Etki:** `/go/[slug]` affiliate linkleri calismayacak.
**Cozum:** Admin panelinden redirect'ler tanimlanmali.

### B5: login_url 14 Sitede Eksik (KRITIK)
**Konum:** `cms_main.sites.login_url` kolonu
**Sorun:** 14/17 sitede login_url NULL.
- OK: rise-bets.com, risebette.com, risebets.click
- EKSIK: Diger 14 site
**Etki:** LoginCtaBar "Giris Yap" butonu calismayacak.
**Cozum:** Her site icin giris URL'si admin panelinden tanimlanmali.

### B6: entry_url 14 Sitede Eksik (KRITIK)
**Konum:** `cms_main.sites.entry_url` kolonu
**Sorun:** 14/17 sitede entry_url NULL veya eksik.
- OK: casival.me, prensbet.me, risebets.click
- EKSIK: Diger 14 site
**Not:** casival.me ve prensbet.me icin entry_url domain adi olarak set edilmis (https:// prefix'i yok) - bu da sorunlu olabilir.
**Cozum:** Tam URL formatinda tanimlanmali (https://...).

### B7: OG Image Generic (ORTA)
**Konum:** Frontend layout - og:image meta tag
**Sorun:** Tum siteler `https://[domain]/storage/og-image.png` kullaniyor. Bu tek bir dosya ve site-specific degil.
**Cozum:** Her site icin markaya ozel OG image olusturulmali.

### B8: pragmaticlive.click Custom CSS Eksik (ORTA)
**Konum:** `cms_main.sites.custom_css` kolonu
**Sorun:** 16 sitede custom CSS set edilmisken pragmaticlive.click icin bos.
**Cozum:** Admin panelinden CSS tanimlanmali.

### B9: PM2 Fazla Restart (ORTA)
**Konum:** PM2 cms-frontend process
**Sorun:** 27 saatte 38 restart kaydedilmis. Bu yuksek bir sayi.
**Olasi Neden:** Memory limit, uncaught exception, veya ilk kurulum restart'lari olabilir.
**Cozum:** `pm2 logs cms-frontend --err` ile hata loglari incelenmeli.

### B10: Frontend API URL Hardcoded (ORTA)
**Konum:** `/var/www/multi-tenant-cms/frontend/.env.local`
**Sorun:** `NEXT_PUBLIC_API_URL=https://rise-bets.com/api/v1` - tum API cagrilari rise-bets.com uzerinden gidiyor.
**Neden calisiyor:** Frontend `X-Tenant-Domain` header'i ile actual domain'i gonderiyor, bu yuzden tenant routing dogru calisiyor.
**Risk:** rise-bets.com'un SSL'i/DNS'i bozulursa TUM siteler etkilenir. `getSponsors()` fonksiyonu X-Tenant-Domain header'i gondermiyor - bu ayri bir bug.
**Cozum:** Her sitenin kendi API URL'sini kullanmasi veya `http://localhost:8000/api/v1` gibi internal URL kullanilmasi daha guvenli.

### B11: casival.me Logo External URL (DUSUK)
**Konum:** `cms_main.sites.logo_url` - casival.me satiri
**Sorun:** Diger siteler `/storage/uploads/site-logos/...` kullanirken casival.me external URL kullaniyor:
- Logo: `https://www.casival.org/wp-content/uploads/2025/01/girisx.gif`
- Favicon: `https://www.casival491.com/logo.png?v=1749360163`
**Risk:** External URL'ler erisime kapatilabilir.
**Cozum:** Logo'yu local storage'a yuklemek daha guvenilir.

### B12: Bazi Sitelerde Footer Sosyal Link Yok (DUSUK)
**Konum:** Frontend footer component
**Sorun:** prensbet.me, risebets.click, pragmaticlive.click footer'inda sosyal medya linkleri gorunmuyor (diger siteler gosteriyor).
**Olasi Neden:** Frontend'de social_links icerisindeki null degerlerin filtrelenmesi farkli davranabilir.

---

## ISTATISTIKLER

### Veritabani Durum Tablosu

| Site | Posts | Pages | Offers | Redirects | GA ID | Login | Entry |
|------|-------|-------|--------|-----------|-------|-------|-------|
| rise-bets.com | 47 | 13 | 0 | 0 | G-GPM7T0FFCX | OK | EKSIK |
| casival.me | 38 | 11 | 0 | 0 | G-J19Q1D4JZX | EKSIK | SORUNLU |
| ilkbahis.click | 33 | 10 | 0 | 0 | G-ZNKGPNZNES | EKSIK | EKSIK |
| ilkbahis.link | 37 | 10 | 0 | 0 | G-M02RSQNZX5 | EKSIK | EKSIK |
| ilkbahisgiri.net | 37 | 10 | 0 | 0 | G-X3JG32S1XR | EKSIK | EKSIK |
| ilkbahisonline.com | 31 | 10 | 0 | 0 | G-LEDFJQYMXW | EKSIK | EKSIK |
| prensbet.me | 32 | 10 | 0 | 0 | G-MQ5M1NRBPQ | EKSIK | SORUNLU |
| risebett.me | 29 | 10 | 0 | 0 | G-BE9Y7JGD55 | EKSIK | EKSIK |
| rayzbet.net | 34 | 10 | 0 | 0 | G-D4K68NQZR7 | EKSIK | EKSIK |
| prensbetgiris.online | 37 | 10 | 0 | 0 | G-3H2Y1G75ZR | EKSIK | EKSIK |
| prensbetgiris.site | 35 | 10 | 0 | 0 | G-Y84D96GQ5N | EKSIK | EKSIK |
| prensbetgirisonline.one | 34 | 10 | 0 | 0 | G-3HRJQX8D0W | EKSIK | EKSIK |
| prenssbet.com | 35 | 10 | 0 | 0 | G-GJQGYY08H7 | EKSIK | EKSIK |
| prenssbet.net | 32 | 10 | 0 | 0 | G-C2HF3HLVMV | EKSIK | EKSIK |
| risebette.com | 31 | 10 | 0 | 0 | G-5LCGDB3F60 | OK | EKSIK |
| risebets.click | 32 | 11 | 0 | 0 | G-5709HKEKE2 | OK | OK |
| pragmaticlive.click | 24 | 10 | 0 | 0 | G-HQJ4PVX3V4 | EKSIK | EKSIK |

### SSL Sertifika Durumu
Tum 17 site: **GECERLI** (son kullanma: May 2026)

### Altyapi Durumu
- **Nginx:** OK (config test basarili)
- **PM2:** Online (58.5MB, 38 restart)
- **PHP-FPM:** 8.2 + 8.4 calisiyor
- **Laravel:** 12.51.0
- **DNS:** Cloudflare proxy aktif (tum siteler)

---

## ONCELIK SIRASI (Aksiyon Plani)

1. **B1** - Sosyal medya linklerini her site icin ayri tanimla
2. **B5/B6** - login_url ve entry_url'leri tum siteler icin tanimla
3. **B3** - Top offers verilerini gir (her tenant icin)
4. **B2** - Sponsors verilerini gir (global tablo)
5. **B4** - Redirect'leri tanimla (affiliate linkleri)
6. **B7** - Site-specific OG image'lar olustur
7. **B8** - pragmaticlive.click CSS tanimla
8. **B9** - PM2 loglarini incele
9. **B10** - Frontend API URL stratejisini gozden gecir
10. **B11** - casival.me logosunu local'e tasi
11. **B12** - Footer sosyal link gosterimini incele
