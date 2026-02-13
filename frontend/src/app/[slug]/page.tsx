import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { getCurrentDomain } from '@/lib/domain';
import { getPage } from '@/lib/api';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  try {
    const res = await getPage(domain, slug);
    const page = res.data;

    return {
      title: page.meta_title || page.title,
      description: page.meta_description || undefined,
      keywords: page.meta_keywords || undefined,
    };
  } catch {
    return {
      title: 'Page Not Found',
    };
  }
}

export default async function DynamicPage({ params }: PageProps) {
  const { slug } = await params;
  const domain = await getCurrentDomain();

  let page;

  try {
    const res = await getPage(domain, slug);
    page = res.data;
  } catch {
    notFound();
  }

  if (!page) {
    notFound();
  }

  return (
    <div className="page-content">
      <h1>{page.title}</h1>
      <div dangerouslySetInnerHTML={{ __html: page.content }} />
    </div>
  );
}
