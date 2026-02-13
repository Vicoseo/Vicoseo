<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create
        {domain : The domain name for the new site (e.g., example.com)}
        {name : The display name for the site (e.g., "Example Site")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant site with its own database';

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
        $domain = $this->argument('domain');
        $name = $this->argument('name');

        // Validate domain is not already registered
        $existingSite = Site::where('domain', $domain)->first();
        if ($existingSite) {
            $this->error("A site with domain '{$domain}' already exists (ID: {$existingSite->id}).");
            return self::FAILURE;
        }

        $this->info("Creating tenant for domain: {$domain}");

        try {
            // Step 1: Create the site record in the landlord database
            $site = Site::create([
                'domain' => $domain,
                'name' => $name,
                'db_name' => 'placeholder',
                'is_active' => true,
                'primary_color' => '#000000',
                'secondary_color' => '#ffffff',
            ]);

            // Step 2: Generate the tenant database name using the site ID
            $dbName = 'tenant_' . $site->id;
            $site->update(['db_name' => $dbName]);

            $this->info("  Site record created (ID: {$site->id})");
            $this->info("  Database name: {$dbName}");

            // Step 3: Create the tenant database and run migrations
            // Note: CREATE DATABASE is DDL and cannot be wrapped in a transaction
            $this->info('  Creating tenant database...');
            $this->tenantManager->createTenantDatabase($site);

            $this->newLine();
            $this->info('Tenant created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Site ID', $site->id],
                    ['Domain', $site->domain],
                    ['Name', $site->name],
                    ['Database', $site->db_name],
                    ['Active', 'Yes'],
                ]
            );

            $this->newLine();
            $this->info('Next steps:');
            $this->line("  1. Point DNS for '{$domain}' to your server IP");
            $this->line("  2. Issue SSL certificate: sudo certbot certonly --webroot -w /var/www/certbot -d {$domain}");
            $this->line("  3. Optionally seed sample data: php artisan tenant:seed {$domain}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            // Clean up: delete the site record if it was created
            if (isset($site)) {
                $site->delete();
            }

            $this->error('Failed to create tenant: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
