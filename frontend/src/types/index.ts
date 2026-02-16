export interface FooterLink {
  id: number;
  link_text: string;
  link_url: string;
}

export interface SocialLinks {
  telegram?: string;
  instagram?: string;
  x?: string;
  youtube?: string;
  tiktok?: string;
  whatsapp?: string;
  support_email?: string;
}

export interface SiteConfig {
  id: number;
  domain: string;
  name: string;
  logo_url: string | null;
  favicon_url: string | null;
  primary_color: string;
  secondary_color: string;
  meta_title: string | null;
  meta_description: string | null;
  entry_url: string | null;
  login_url: string | null;
  show_sponsors: boolean;
  social_links: SocialLinks | null;
  ga_measurement_id: string | null;
  footer_links: FooterLink[];
}

export interface Page {
  id: number;
  slug: string;
  title: string;
  content: string;
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  sort_order: number;
  created_at?: string;
  updated_at?: string;
}

export interface Post {
  id: number;
  slug: string;
  title: string;
  excerpt: string | null;
  content: string;
  featured_image: string | null;
  meta_title: string | null;
  meta_description: string | null;
  published_at: string;
  created_at?: string;
  updated_at?: string;
}

export interface TopOffer {
  id: number;
  slug: string;
  logo_url: string;
  bonus_text: string;
  cta_text: string;
  target_url: string;
  sort_order: number;
  source: 'global' | 'site';
  is_active: boolean;
}

export interface Sponsor {
  id: number;
  name: string;
  url: string;
  primary_color: string;
  web_background: string | null;
  mobile_background: string | null;
  logo: string | null;
  rating: number;
  sort_order: number;
  promotions: { highlight: string; text: string }[];
}

export interface RedirectResponse {
  target_url: string;
  status: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ApiResponse<T> {
  data: T;
}
