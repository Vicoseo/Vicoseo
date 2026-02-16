<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\ImageGenerationService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateDailyContent extends Command
{
    protected $signature = 'content:generate-daily
        {--site= : Generate only for a specific domain}
        {--count=2 : Number of posts to generate per site}
        {--dry-run : Show what would be generated without actually creating content}';

    protected $description = 'Generate daily blog posts with AI for all active sites';

    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiService,
        private ImageGenerationService $imageService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $dryRun = (bool) $this->option('dry-run');
        $siteFilter = $this->option('site');

        $this->info('Starting daily content generation...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - no content will be created.');
        }

        // Get active sites
        $query = Site::where('is_active', true);
        if ($siteFilter) {
            $query->where('domain', $siteFilter);
        }
        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->error('No active sites found.');
            return 1;
        }

        $this->info("Found {$sites->count()} active site(s).");
        $totalCreated = 0;
        $totalErrors = 0;

        foreach ($sites as $site) {
            $this->newLine();
            $this->info("Processing: {$site->name} ({$site->domain})");

            try {
                // Connect to tenant DB
                $this->tenantManager->setTenant($site);

                // Get existing post slugs
                $existingSlugs = Post::pluck('slug')->toArray();
                $this->line("  Existing posts: " . count($existingSlugs));

                // Pick unused topics
                $topics = $this->aiService->pickTopics($site->name, $existingSlugs, $count);

                if (empty($topics)) {
                    $this->warn("  No unused topics available for {$site->name}. All topics exhausted.");
                    Log::info("DailyContent: No unused topics for {$site->domain}");
                    continue;
                }

                $this->line("  Selected {$count} topic(s) from " . count($this->aiService->getAvailableTopics($site->name, $existingSlugs)) . " available.");

                foreach ($topics as $topic) {
                    $topicName = str_replace(['{brand}', '{year}'], [$site->name, date('Y')], $topic['topic']);
                    $this->line("  Generating: {$topicName}");

                    if ($dryRun) {
                        $this->line("    [DRY RUN] Would create: {$topic['full_slug']}");
                        continue;
                    }

                    try {
                        // Generate the post content
                        $siteInfo = [
                            'country' => 'Türkiye',
                            'social_links' => $site->social_links ?? [],
                        ];
                        $generated = $this->aiService->generateDailyPost(
                            $site->name,
                            $site->domain,
                            $topic['topic'],
                            $topic['instructions'],
                            $existingSlugs,
                            $siteInfo
                        );

                        // Generate featured image
                        $featuredImage = null;
                        $imagePrompt = $generated['featured_image_prompt'] ?? null;
                        if (!empty($imagePrompt)) {
                            $this->line("    Generating featured image...");
                            $featuredImage = $this->imageService->generateFeaturedImage(
                                $topicName,
                                $site->name,
                                $imagePrompt
                            );
                        } else {
                            $this->line("    Generating featured image (default prompt)...");
                            $featuredImage = $this->imageService->generateFeaturedImage(
                                $topicName,
                                $site->name
                            );
                        }

                        // Save the post
                        Post::create([
                            'slug' => $topic['full_slug'],
                            'title' => $generated['title'],
                            'excerpt' => $generated['excerpt'] ?? '',
                            'content' => $generated['content'],
                            'featured_image' => $featuredImage,
                            'meta_title' => $generated['meta_title'] ?? $generated['title'],
                            'meta_description' => $generated['meta_description'] ?? '',
                            'is_published' => true,
                            'published_at' => now(),
                        ]);

                        $existingSlugs[] = $topic['full_slug'];
                        $totalCreated++;
                        $imageStatus = $featuredImage ? 'with image' : 'no image';
                        $this->info("    Created: {$generated['title']} ({$imageStatus})");

                        Log::info("DailyContent: Created post", [
                            'site' => $site->domain,
                            'slug' => $topic['full_slug'],
                            'has_image' => !empty($featuredImage),
                        ]);
                    } catch (\Throwable $e) {
                        $totalErrors++;
                        $this->error("    Error: {$e->getMessage()}");
                        Log::error("DailyContent: Failed to generate post", [
                            'site' => $site->domain,
                            'topic' => $topicName,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                $totalErrors++;
                $this->error("  Site error: {$e->getMessage()}");
                Log::error("DailyContent: Site processing failed", [
                    'site' => $site->domain,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Done! Created: {$totalCreated} posts, Errors: {$totalErrors}");

        Log::info("DailyContent: Completed", [
            'created' => $totalCreated,
            'errors' => $totalErrors,
        ]);

        return $totalErrors > 0 && $totalCreated === 0 ? 1 : 0;
    }
}
