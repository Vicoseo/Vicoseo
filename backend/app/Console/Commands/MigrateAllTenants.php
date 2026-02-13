<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateAllTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-all
        {--force : Force the operation to run in production}
        {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tenant migrations on all active tenant databases';

    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sites = Site::where('is_active', true)->orderBy('id')->get();

        if ($sites->isEmpty()) {
            $this->warn('No active sites found. Nothing to migrate.');
            return self::SUCCESS;
        }

        $this->info("Found {$sites->count()} active site(s) to migrate.");
        $this->newLine();

        $successCount = 0;
        $failureCount = 0;
        $failures = [];

        $progressBar = $this->output->createProgressBar($sites->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $progressBar->start();

        foreach ($sites as $site) {
            $progressBar->setMessage("Migrating: {$site->domain} ({$site->db_name})");

            try {
                // Configure the tenant connection for this site
                $this->tenantManager->setTenant($site);

                // Run tenant migrations on the tenant connection
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--database' => 'tenant',
                    '--force' => true,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $failureCount++;
                $failures[] = [
                    'domain' => $site->domain,
                    'db_name' => $site->db_name,
                    'error' => $e->getMessage(),
                ];
            }

            $progressBar->advance();
        }

        $progressBar->setMessage('Complete!');
        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Migration complete: {$successCount} succeeded, {$failureCount} failed.");

        if (! empty($failures)) {
            $this->newLine();
            $this->error('The following sites failed to migrate:');
            $this->table(
                ['Domain', 'Database', 'Error'],
                array_map(fn ($f) => [$f['domain'], $f['db_name'], $f['error']], $failures)
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
