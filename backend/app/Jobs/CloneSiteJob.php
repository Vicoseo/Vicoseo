<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Concerns\HandlesTenantContext;
use App\Models\Site;
use App\Services\CloneService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CloneSiteJob implements ShouldQueue
{
    use Batchable, HandlesTenantContext, Queueable;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        public int $sourceSiteId,
        public string $targetDomain,
        public int $botTaskId,
    ) {}

    public function handle(CloneService $cloneService): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $this->updateTaskStatus($this->botTaskId, 'processing', 10);

        try {
            $source = Site::findOrFail($this->sourceSiteId);

            $this->updateTaskStatus($this->botTaskId, 'processing', 30);

            $target = $cloneService->cloneSite($source, $this->targetDomain);

            $this->updateTaskStatus($this->botTaskId, 'completed', 100, [
                'target_site_id' => $target->id,
                'domain' => $target->domain,
            ]);
        } catch (\Throwable $e) {
            $this->updateTaskStatus($this->botTaskId, 'failed', 0, null, $e->getMessage());
            throw $e;
        }
    }
}
