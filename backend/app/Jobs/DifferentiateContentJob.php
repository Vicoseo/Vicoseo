<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Concerns\HandlesTenantContext;
use App\Models\Page;
use App\Models\Post;
use App\Services\AIContentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DifferentiateContentJob implements ShouldQueue
{
    use HandlesTenantContext, Queueable;

    public int $tries = 1;
    public int $timeout = 3600;

    public function __construct(
        public int $siteId,
        public int $botTaskId,
        public string $contentType = 'all', // 'pages', 'posts', 'all'
        public bool $fullRewrite = false,
    ) {}

    public function handle(AIContentService $aiService): void
    {
        $this->updateTaskStatus($this->botTaskId, 'processing', 5);

        try {
            $site = $this->resolveTenant($this->siteId);

            if (empty($site->content_identity)) {
                $this->updateTaskStatus($this->botTaskId, 'failed', 0, null, 'No content_identity set for this site.');
                return;
            }

            $processed = 0;
            $errors = [];

            // Pages
            if (in_array($this->contentType, ['pages', 'all'])) {
                $pages = Page::all();
                $totalPages = $pages->count();

                foreach ($pages as $i => $page) {
                    if ($page->content_differentiated_at) {
                        $processed++;
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

                        $processed++;
                        Log::info("Differentiated page {$page->slug} for {$site->domain}");
                    } catch (\Throwable $e) {
                        $errors[] = "Page '{$page->slug}': {$e->getMessage()}";
                        Log::error("Failed to differentiate page {$page->slug} for {$site->domain}: {$e->getMessage()}");
                    }

                    $progress = 5 + (int) (($i + 1) / max($totalPages, 1) * 45);
                    $this->updateTaskStatus($this->botTaskId, 'processing', $progress);
                }
            }

            // Posts
            if (in_array($this->contentType, ['posts', 'all'])) {
                $posts = Post::all();
                $totalPosts = $posts->count();

                foreach ($posts as $i => $post) {
                    if ($post->content_differentiated_at) {
                        $processed++;
                        continue;
                    }

                    try {
                        if ($this->fullRewrite) {
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
                        } else {
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
                        }

                        $processed++;
                        Log::info("Differentiated post {$post->slug} for {$site->domain}");
                    } catch (\Throwable $e) {
                        $errors[] = "Post '{$post->slug}': {$e->getMessage()}";
                        Log::error("Failed to differentiate post {$post->slug} for {$site->domain}: {$e->getMessage()}");
                    }

                    $progress = 50 + (int) (($i + 1) / max($totalPosts, 1) * 48);
                    $this->updateTaskStatus($this->botTaskId, 'processing', min($progress, 98));
                }
            }

            $this->updateTaskStatus($this->botTaskId, 'completed', 100, [
                'site_id' => $this->siteId,
                'domain' => $site->domain,
                'processed' => $processed,
                'errors' => $errors,
            ]);
        } catch (\Throwable $e) {
            $this->updateTaskStatus($this->botTaskId, 'failed', 0, null, $e->getMessage());
            throw $e;
        }
    }
}
