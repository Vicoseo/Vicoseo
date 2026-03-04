<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\TopOffer;
use Illuminate\Support\Facades\Log;

class CloneService
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * Clone a source site to a new domain. Creates new site record, tenant DB,
     * and copies all content from the source site.
     */
    public function cloneSite(Site $source, string $domain): Site
    {
        // Read source content once
        $sourceContent = $this->readSourceContent($source);

        return $this->createTargetSite($source, $domain, $sourceContent);
    }

    /**
     * Batch clone a source site to multiple domains.
     * Reads source content once, then creates all targets.
     *
     * @return array{succeeded: Site[], failed: array{domain: string, error: string}[]}
     */
    public function batchClone(Site $source, array $domains): array
    {
        $sourceContent = $this->readSourceContent($source);

        $succeeded = [];
        $failed = [];

        foreach ($domains as $domain) {
            try {
                $succeeded[] = $this->createTargetSite($source, $domain, $sourceContent);
            } catch (\Exception $e) {
                Log::error("Batch clone failed for {$domain}", ['error' => $e->getMessage()]);
                $failed[] = ['domain' => $domain, 'error' => $e->getMessage()];
            }
        }

        return ['succeeded' => $succeeded, 'failed' => $failed];
    }

    /**
     * Read all content from a source site.
     */
    private function readSourceContent(Site $source): array
    {
        $this->tenantManager->setTenant($source);

        return [
            'pages' => Page::all()->toArray(),
            'posts' => Post::all()->toArray(),
            'topOffers' => TopOffer::all()->toArray(),
            'redirects' => Redirect::all()->toArray(),
        ];
    }

    /**
     * Create a target site and copy source content into it.
     */
    private function createTargetSite(Site $source, string $domain, array $content): Site
    {
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

        $this->tenantManager->createTenantDatabase($target);
        $this->tenantManager->setTenant($target);

        foreach ($content['pages'] as $page) {
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

        foreach ($content['posts'] as $post) {
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

        foreach ($content['topOffers'] as $offer) {
            TopOffer::create([
                'logo_url' => $offer['logo_url'],
                'bonus_text' => $offer['bonus_text'],
                'cta_text' => $offer['cta_text'],
                'target_url' => $offer['target_url'],
                'sort_order' => $offer['sort_order'],
                'is_active' => $offer['is_active'],
            ]);
        }

        foreach ($content['redirects'] as $redirect) {
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
