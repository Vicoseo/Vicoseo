import type { Metadata } from 'next';
import { getCurrentDomain } from '@/lib/domain';
import { getPages, getPage, getSiteConfig } from '@/lib/api';

export async function generateMetadata(): Promise<Metadata> {
  const domain = await getCurrentDomain();

  try {
    const [siteRes, pagesRes] = await Promise.all([
      getSiteConfig(domain),
      getPages(domain, 1),
    ]);

    const firstPage = pagesRes.data?.[0];
    const siteUrl = `https://${siteRes.data.domain}`;

    return {
      title: firstPage?.meta_title || siteRes.data.meta_title || siteRes.data.name,
      description:
        firstPage?.meta_description ||
        siteRes.data.meta_description ||
        `${siteRes.data.name} - Online bahis ve casino platformu`,
      keywords: firstPage?.meta_keywords || undefined,
      alternates: {
        canonical: siteUrl,
      },
      openGraph: {
        title: firstPage?.meta_title || siteRes.data.meta_title || siteRes.data.name,
        description:
          firstPage?.meta_description ||
          siteRes.data.meta_description ||
          `${siteRes.data.name} - Online bahis ve casino platformu`,
        url: siteUrl,
        type: 'website',
        locale: 'tr_TR',
        siteName: siteRes.data.name,
      },
    };
  } catch {
    return {
      title: 'Home',
    };
  }
}

export default async function HomePage() {
  const domain = await getCurrentDomain();

  let firstPage = null;
  let siteName = 'our site';

  try {
    const [pagesRes, siteRes] = await Promise.all([
      getPages(domain, 1),
      getSiteConfig(domain),
    ]);
    siteName = siteRes.data.name;

    const firstSlug = pagesRes.data?.[0]?.slug;
    if (firstSlug) {
      const pageRes = await getPage(domain, firstSlug);
      firstPage = pageRes.data;
    }
  } catch {
    // API unavailable - render fallback
  }

  if (!firstPage) {
    return (
      <div className="page-content">
        <h1>Welcome to {siteName}</h1>
        <p>
          This is the homepage. Content will appear here once pages have been
          created in the CMS.
        </p>
      </div>
    );
  }

  return (
    <div className="page-content">
      <h1>{firstPage.title}</h1>
      <div dangerouslySetInnerHTML={{ __html: firstPage.content }} />
    </div>
  );
}
