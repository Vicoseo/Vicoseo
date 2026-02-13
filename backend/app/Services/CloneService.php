<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\TopOffer;

class CloneService
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * Clone a source site to a new domain. Creates new site record, tenant DB,
     * and copies all content from the source site.
     */
    public function cloneSite(Site $source, string $domain): Site
    {
        // Create new site record with source config
        $dbName = 'tenant_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($domain));

        $target = Site::create([
            'domain' => $domain,
            'name' => $source->name,
            'logo_url' => $source->logo_url,
            'favicon_url' => $source->favicon_url,
            'primary_color' => $source->primary_color,
            'secondary_color' => $source->secondary_color,
            'db_name' => $dbName,
            'is_active' => true,
            'meta_title' => $source->meta_title,
            'meta_description' => $source->meta_description,
            'entry_url' => $source->entry_url,
            'show_sponsors' => $source->show_sponsors,
        ]);

        // Create tenant database and run migrations
        $this->tenantManager->createTenantDatabase($target);

        // Read all content from source
        $this->tenantManager->setTenant($source);
        $pages = Page::all()->toArray();
        $posts = Post::all()->toArray();
        $topOffers = TopOffer::all()->toArray();
        $redirects = Redirect::all()->toArray();

        // Switch to target and write content
        $this->tenantManager->setTenant($target);

        foreach ($pages as $page) {
            Page::create([
                'slug' => $page['slug'],
                'title' => $page['title'],
                'content' => $page['content'],
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'meta_keywords' => $page['meta_keywords'],
                'is_published' => $page['is_published'],
                'sort_order' => $page['sort_order'],
            ]);
        }

        foreach ($posts as $post) {
            Post::create([
                'slug' => $post['slug'],
                'title' => $post['title'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'featured_image' => $post['featured_image'],
                'meta_title' => $post['meta_title'],
                'meta_description' => $post['meta_description'],
                'is_published' => $post['is_published'],
                'published_at' => $post['published_at'],
            ]);
        }

        foreach ($topOffers as $offer) {
            TopOffer::create([
                'logo_url' => $offer['logo_url'],
                'bonus_text' => $offer['bonus_text'],
                'cta_text' => $offer['cta_text'],
                'target_url' => $offer['target_url'],
                'sort_order' => $offer['sort_order'],
                'is_active' => $offer['is_active'],
            ]);
        }

        foreach ($redirects as $redirect) {
            Redirect::create([
                'slug' => $redirect['slug'],
                'target_url' => $redirect['target_url'],
                'click_count' => 0,
                'is_active' => $redirect['is_active'],
            ]);
        }

        return $target;
    }
}
