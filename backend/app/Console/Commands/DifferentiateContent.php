<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BotTask;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\TenantManager;
use Illuminate\Console\Command;

class DifferentiateContent extends Command
{
    protected $signature = 'content:differentiate
        {--site= : Single site domain to differentiate}
        {--type=all : Content type: pages, posts, or all}
        {--dry-run : Preview changes without saving}
        {--force : Re-differentiate already processed items}
        {--full-rewrite : Full content rewrite for posts (expensive)}';

    protected $description = 'Differentiate content across sites to make each site unique';

    private int $processed = 0;
    private int $skipped = 0;
    private int $errors = 0;
    private ?BotTask $botTask = null;

    public function handle(AIContentService $aiService, TenantManager $tenantManager): int
    {
        $type = $this->option('type');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $fullRewrite = (bool) $this->option('full-rewrite');
        $siteDomain = $this->option('site');

        if ($dryRun) {
            $this->info('[DRY RUN] No changes will be saved.');
        }

        // Get sites to process
        $sites = $this->getSites($siteDomain);
        if ($sites->isEmpty()) {
            $this->error('No sites found to process.');
            return self::FAILURE;
        }

        $this->info("Processing {$sites->count()} site(s)...");

        // Create BotTask for tracking (unless dry-run)
        if (!$dryRun) {
            $this->botTask = BotTask::create([
                'type' => 'ai_spin',
                'status' => 'processing',
                'payload' => [
                    'action' => 'differentiate',
                    'type' => $type,
                    'site' => $siteDomain,
                    'force' => $force,
                    'full_rewrite' => $fullRewrite,
                ],
                'progress' => 0,
            ]);
        }

        $totalSites = $sites->count();

        foreach ($sites as $index => $site) {
            $this->newLine();
            $this->info("=== [{$site->domain}] ===");

            if (empty($site->content_identity)) {
                $this->warn("  No content_identity set for {$site->domain}, skipping.");
                $this->skipped++;
                continue;
            }

            $tenantManager->setTenant($site);

            if (in_array($type, ['pages', 'all'])) {
                $this->differentiatePages($aiService, $site, $dryRun, $force);
            }

            if (in_array($type, ['posts', 'all'])) {
                if ($fullRewrite) {
                    $this->differentiatePostsFull($aiService, $site, $dryRun, $force);
                } else {
                    $this->differentiatePostsMeta($aiService, $site, $dryRun, $force);
                }
            }

            // Update progress
            $progress = (int) (($index + 1) / $totalSites * 100);
            $this->updateProgress($progress);
        }

        $this->newLine();
        $this->info("=== SUMMARY ===");
        $this->info("Processed: {$this->processed}");
        $this->info("Skipped: {$this->skipped}");
        $this->info("Errors: {$this->errors}");

        if ($this->botTask) {
            $this->botTask->update([
                'status' => 'completed',
                'progress' => 100,
                'result' => [
                    'processed' => $this->processed,
                    'skipped' => $this->skipped,
                    'errors' => $this->errors,
                ],
            ]);
        }

        return self::SUCCESS;
    }

    private function getSites(?string $domain): \Illuminate\Database\Eloquent\Collection
    {
        $query = Site::where('is_active', true);

        if ($domain) {
            $query->where('domain', $domain);
        }

        return $query->get();
    }

    private function differentiatePages(AIContentService $aiService, Site $site, bool $dryRun, bool $force): void
    {
        $pages = Page::all();
        $this->info("  Pages: {$pages->count()} found");

        foreach ($pages as $page) {
            // Skip if already differentiated (unless --force)
            if (!$force && $page->content_differentiated_at) {
                $this->line("  [SKIP] {$page->slug} — already differentiated");
                $this->skipped++;
                continue;
            }

            $this->line("  [PAGE] {$page->slug} — spinning content...");

            if ($dryRun) {
                $this->line("    [DRY RUN] Would rewrite: title, content, meta_title, meta_description");
                $this->processed++;
                continue;
            }

            try {
                $original = [
                    'title' => $page->title,
                    'content' => $page->content,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                ];

                $result = $aiService->spinForSite($original, $site);

                $page->update([
                    'title' => $result['title'],
                    'content' => $result['content'],
                    'meta_title' => $result['meta_title'],
                    'meta_description' => $result['meta_description'],
                    'content_differentiated_at' => now(),
                ]);

                $this->info("    OK — new title: {$result['meta_title']}");
                $this->processed++;
            } catch (\Throwable $e) {
                $this->error("    ERROR: {$e->getMessage()}");
                $this->errors++;
            }
        }
    }

    private function differentiatePostsMeta(AIContentService $aiService, Site $site, bool $dryRun, bool $force): void
    {
        $posts = Post::all();
        $this->info("  Posts (meta only): {$posts->count()} found");

        foreach ($posts as $post) {
            if (!$force && $post->content_differentiated_at) {
                $this->line("  [SKIP] {$post->slug} — already differentiated");
                $this->skipped++;
                continue;
            }

            $this->line("  [POST META] {$post->slug}");

            if ($dryRun) {
                $this->line("    [DRY RUN] Would rewrite: meta_title, meta_description, excerpt");
                $this->processed++;
                continue;
            }

            try {
                $original = [
                    'title' => $post->title,
                    'meta_title' => $post->meta_title,
                    'meta_description' => $post->meta_description,
                    'excerpt' => $post->excerpt,
                ];

                $result = $aiService->rewriteMeta($original, $site);

                $post->update([
                    'title' => $result['title'],
                    'meta_title' => $result['meta_title'],
                    'meta_description' => $result['meta_description'],
                    'excerpt' => $result['excerpt'],
                    'content_differentiated_at' => now(),
                ]);

                $this->info("    OK — new meta: {$result['meta_title']}");
                $this->processed++;
            } catch (\Throwable $e) {
                $this->error("    ERROR: {$e->getMessage()}");
                $this->errors++;
            }
        }
    }

    private function differentiatePostsFull(AIContentService $aiService, Site $site, bool $dryRun, bool $force): void
    {
        $posts = Post::all();
        $this->info("  Posts (full rewrite): {$posts->count()} found");

        foreach ($posts as $post) {
            if (!$force && $post->content_differentiated_at) {
                $this->line("  [SKIP] {$post->slug} — already differentiated");
                $this->skipped++;
                continue;
            }

            $this->line("  [POST FULL] {$post->slug} — full rewrite...");

            if ($dryRun) {
                $this->line("    [DRY RUN] Would fully rewrite: title, content, meta_title, meta_description, excerpt");
                $this->processed++;
                continue;
            }

            try {
                $original = [
                    'title' => $post->title,
                    'content' => $post->content,
                    'meta_title' => $post->meta_title,
                    'meta_description' => $post->meta_description,
                ];

                $result = $aiService->spinForSite($original, $site);

                $post->update([
                    'title' => $result['title'],
                    'content' => $result['content'],
                    'meta_title' => $result['meta_title'],
                    'meta_description' => $result['meta_description'],
                    'excerpt' => $result['excerpt'] ?? $post->excerpt,
                    'content_differentiated_at' => now(),
                ]);

                $this->info("    OK — new title: {$result['meta_title']}");
                $this->processed++;
            } catch (\Throwable $e) {
                $this->error("    ERROR: {$e->getMessage()}");
                $this->errors++;
            }
        }
    }

    private function updateProgress(int $progress): void
    {
        if ($this->botTask) {
            $this->botTask->update(['progress' => $progress]);
        }
    }
}
