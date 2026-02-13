<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Models\BotTask;
use App\Models\Site;
use App\Services\TenantManager;

trait HandlesTenantContext
{
    protected function resolveTenant(int $siteId): Site
    {
        $site = Site::findOrFail($siteId);
        app(TenantManager::class)->setTenant($site);

        return $site;
    }

    protected function updateTaskStatus(int $taskId, string $status, int $progress = 0, ?array $result = null, ?string $error = null): void
    {
        $data = [
            'status' => $status,
            'progress' => $progress,
        ];

        if ($result !== null) {
            $data['result'] = $result;
        }

        if ($error !== null) {
            $data['error_message'] = $error;
        }

        BotTask::where('id', $taskId)->update($data);
    }
}
