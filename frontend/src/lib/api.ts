import { SiteConfig, Page, Post, Category, TopOffer, Sponsor, PaginatedResponse, ApiResponse, RedirectResponse } from '@/types';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api/v1';

async function fetchApi<T>(
  endpoint: string,
  domain: string,
  options: RequestInit = {}
): Promise<T> {
  const url = `${API_URL}${endpoint}`;

  const res = await fetch(url, {
    next: { revalidate: 300 },
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Tenant-Domain': domain,
      ...options.headers,
    },
  });

  if (!res.ok) {
    throw new Error(`API error: ${res.status} ${res.statusText}`);
  }

  return res.json();
}

export function getSiteConfig(domain: string): Promise<ApiResponse<SiteConfig>> {
  return fetchApi<ApiResponse<SiteConfig>>('/site/config', domain);
}

export function getPages(domain: string, page = 1, perPage = 15): Promise<PaginatedResponse<Page>> {
  return fetchApi<PaginatedResponse<Page>>(`/pages?page=${page}&per_page=${perPage}`, domain);
}

export function getPage(domain: string, slug: string): Promise<ApiResponse<Page>> {
  return fetchApi<ApiResponse<Page>>(`/pages/${slug}`, domain);
}

export function getPosts(domain: string, page = 1, perPage = 15): Promise<PaginatedResponse<Post>> {
  return fetchApi<PaginatedResponse<Post>>(`/posts?page=${page}&per_page=${perPage}`, domain);
}

export function getPost(domain: string, slug: string): Promise<ApiResponse<Post>> {
  return fetchApi<ApiResponse<Post>>(`/posts/${slug}`, domain);
}

export function getCategories(domain: string): Promise<ApiResponse<Category[]>> {
  return fetchApi<ApiResponse<Category[]>>('/categories', domain);
}

export function getCategory(domain: string, slug: string): Promise<{ data: Category; posts: PaginatedResponse<Post> }> {
  return fetchApi<{ data: Category; posts: PaginatedResponse<Post> }>(`/categories/${slug}`, domain);
}

export function getTopOffers(domain: string): Promise<ApiResponse<TopOffer[]>> {
  return fetchApi<ApiResponse<TopOffer[]>>('/top-offers', domain);
}

export function getSponsors(category?: string): Promise<ApiResponse<Sponsor[]>> {
  const params = category ? `?category=${category}` : '';
  const url = `${API_URL}/sponsors${params}`;
  return fetch(url, {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    next: { revalidate: 300 },
  }).then((res) => res.json());
}

export function getPopularPosts(domain: string, limit = 6): Promise<{ data: Post[]; has_analytics_data: boolean }> {
  return fetchApi<{ data: Post[]; has_analytics_data: boolean }>(`/posts/popular?limit=${limit}`, domain);
}

export function getRedirect(domain: string, slug: string): Promise<ApiResponse<RedirectResponse>> {
  return fetchApi<ApiResponse<RedirectResponse>>(`/go/${slug}`, domain);
}
