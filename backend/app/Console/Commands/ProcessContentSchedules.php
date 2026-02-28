<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ContentSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessContentSchedules extends Command
{
    protected $signature = 'content:process-schedules';

    protected $description = 'Process active content schedules and trigger generation for due items';

    public function handle(): int
    {
        $now = now();

        $schedules = ContentSchedule::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('next_run_at')
                  ->orWhere('next_run_at', '<=', $now);
            })
            ->with('site')
            ->get();

        if ($schedules->isEmpty()) {
            $this->line('No schedules due.');
            return 0;
        }

        $this->info("Found {$schedules->count()} schedule(s) to process.");

        $processed = 0;
        $errors = 0;

        foreach ($schedules as $schedule) {
            $site = $schedule->site;

            if (!$site || !$site->is_active) {
                $this->warn("Skipping schedule #{$schedule->id} — site inactive or missing.");
                continue;
            }

            $this->line("Processing: {$site->domain} (schedule #{$schedule->id})");

            try {
                $exitCode = Artisan::call('content:generate-daily', [
                    '--site' => $site->domain,
                    '--count' => 1,
                ]);

                $schedule->update([
                    'last_run_at' => $now,
                    'next_run_at' => $this->calculateNextRun($schedule),
                ]);

                $processed++;
                $this->info("  Done — next run: {$schedule->fresh()->next_run_at}");

                Log::info('ProcessSchedules: Completed', [
                    'site' => $site->domain,
                    'schedule_id' => $schedule->id,
                    'exit_code' => $exitCode,
                ]);
            } catch (\Throwable $e) {
                $errors++;
                $this->error("  Error: {$e->getMessage()}");

                Log::error('ProcessSchedules: Failed', [
                    'site' => $site->domain,
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Done! Processed: {$processed}, Errors: {$errors}");

        return $errors > 0 && $processed === 0 ? 1 : 0;
    }

    private function calculateNextRun(ContentSchedule $schedule): \Carbon\Carbon
    {
        $now = now();

        if ($schedule->frequency === 'custom' && $schedule->interval_hours) {
            return $now->copy()->addHours($schedule->interval_hours);
        }

        // Default: daily at run_at time
        $next = $now->copy()->setTimeFromTimeString($schedule->run_at ?? '06:00');

        if ($next->lte($now)) {
            $next->addDay();
        }

        return $next;
    }
}
