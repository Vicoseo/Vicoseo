<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Concerns\HandlesTenantContext;
use App\Models\Page;
use App\Models\Post;
use App\Services\AIContentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AIGenerateContentJob implements ShouldQueue
{
    use HandlesTenantContext, Queueable;

    public int $tries = 1;
    public int $timeout = 180;

    public function __construct(
        public int $targetSiteId,
        public string $contentType, // 'page' or 'post'
        public string $topic,
        public string $instructions,
        public int $botTaskId,
        public array $siteInfo = [],
    ) {}

    public function handle(AIContentService $aiService): void
    {
        $this->updateTaskStatus($this->botTaskId, 'processing', 10);

        try {
            $this->updateTaskStatus($this->botTaskId, 'processing', 20);

            $result = $aiService->generateContent(
                $this->contentType,
                $this->topic,
                $this->instructions,
                $this->siteInfo
            );

            $this->updateTaskStatus($this->botTaskId, 'processing', 70);

            // Switch to target tenant
            $this->resolveTenant($this->targetSiteId);

            if ($this->contentType === 'post') {
                $record = Post::create([
                    'slug' => $result['slug'],
                    'title' => $result['title'],
                    'excerpt' => $result['excerpt'] ?? null,
                    'content' => $result['content'],
                    'meta_title' => $result['meta_title'] ?? null,
                    'meta_description' => $result['meta_description'] ?? null,
                    'is_published' => false,
                    'published_at' => null,
                ]);
            } else {
                $record = Page::create([
                    'slug' => $result['slug'],
                    'title' => $result['title'],
                    'content' => $result['content'],
                    'meta_title' => $result['meta_title'] ?? null,
                    'meta_description' => $result['meta_description'] ?? null,
                    'is_published' => false,
                    'sort_order' => 0,
                ]);
            }

            $this->updateTaskStatus($this->botTaskId, 'completed', 100, [
                'content_type' => $this->contentType,
                'record_id' => $record->id,
                'title' => $result['title'],
                'slug' => $result['slug'],
            ]);
        } catch (\Throwable $e) {
            $this->updateTaskStatus($this->botTaskId, 'failed', 0, null, $e->getMessage());
            throw $e;
        }
    }
}
