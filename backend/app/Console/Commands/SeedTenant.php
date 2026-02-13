<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\TopOffer;
use App\Services\TenantManager;
use Illuminate\Console\Command;

class SeedTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed
        {domain : The domain of the site to seed (e.g., example.com)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample data (pages, posts, top offers, redirects) into a tenant database';

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

        // Find the site by domain
        $site = Site::where('domain', $domain)->first();

        if (! $site) {
            $this->error("No site found with domain '{$domain}'.");
            return self::FAILURE;
        }

        if (! $site->is_active) {
            $this->warn("Site '{$domain}' exists but is inactive. Seeding anyway.");
        }

        $this->info("Seeding tenant database for: {$domain} ({$site->db_name})");

        try {
            // Configure the tenant connection
            $this->tenantManager->setTenant($site);

            // Seed pages
            $this->seedPages();
            $this->info('  Seeded pages.');

            // Seed posts
            $this->seedPosts();
            $this->info('  Seeded posts.');

            // Seed top offers
            $this->seedTopOffers();
            $this->info('  Seeded top offers.');

            // Seed redirects
            $this->seedRedirects();
            $this->info('  Seeded redirects.');

            $this->newLine();
            $this->info("Sample data seeded successfully for '{$domain}'.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to seed tenant: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Seed sample pages.
     */
    private function seedPages(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<h1>About Us</h1><p>Welcome to our site. We are dedicated to providing the best content and services to our visitors. Our team works hard to keep information accurate and up to date.</p><p>If you have any questions, feel free to reach out through our contact page.</p>',
                'meta_title' => 'About Us',
                'meta_description' => 'Learn more about our site, our mission, and our team.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => '<h1>Contact Us</h1><p>We would love to hear from you. Please use the information below to get in touch with our team.</p><p>Email: contact@example.com</p><p>We aim to respond to all inquiries within 24 hours.</p>',
                'meta_title' => 'Contact Us',
                'meta_description' => 'Get in touch with our team. We are here to help.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information when you visit our site.</p><p>We do not sell or share your personal data with third parties except as required by law.</p>',
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Read our privacy policy to understand how we handle your data.',
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'content' => '<h1>Terms of Service</h1><p>By using our site, you agree to the following terms and conditions. Please read them carefully before continuing.</p><p>We reserve the right to update these terms at any time.</p>',
                'meta_title' => 'Terms of Service',
                'meta_description' => 'Review our terms of service before using the site.',
                'is_published' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }

    /**
     * Seed sample blog posts.
     */
    private function seedPosts(): void
    {
        $posts = [
            [
                'slug' => 'welcome-to-our-site',
                'title' => 'Welcome to Our Site',
                'excerpt' => 'We are excited to launch our new site. Stay tuned for great content and updates.',
                'content' => '<h1>Welcome to Our Site</h1><p>We are thrilled to announce the launch of our brand new website. Our team has been working hard to bring you the best experience possible.</p><p>Stay tuned for regular updates, in-depth articles, and exclusive offers. Make sure to bookmark our site and check back often!</p>',
                'meta_title' => 'Welcome to Our Site',
                'meta_description' => 'Introducing our brand new site. Discover what we have to offer.',
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'slug' => 'top-10-tips-for-beginners',
                'title' => 'Top 10 Tips for Beginners',
                'excerpt' => 'Getting started can be overwhelming. Here are our top 10 tips to help beginners succeed.',
                'content' => '<h1>Top 10 Tips for Beginners</h1><p>Starting something new is always a challenge. Whether you are a complete beginner or just looking to brush up on the basics, these tips will help you get on the right track.</p><ol><li>Start with the fundamentals</li><li>Practice regularly</li><li>Learn from experts</li><li>Stay consistent</li><li>Set realistic goals</li><li>Track your progress</li><li>Join a community</li><li>Ask questions</li><li>Be patient</li><li>Never stop learning</li></ol>',
                'meta_title' => 'Top 10 Tips for Beginners - A Complete Guide',
                'meta_description' => 'Discover our top 10 tips for beginners. Start your journey on the right foot.',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'slug' => 'industry-trends-2025',
                'title' => 'Industry Trends to Watch in 2025',
                'excerpt' => 'A look at the most important trends shaping our industry this year.',
                'content' => '<h1>Industry Trends to Watch in 2025</h1><p>The landscape is evolving rapidly. Here are the key trends we believe will shape the industry in 2025 and beyond.</p><p>From technology advancements to changing consumer preferences, staying informed about these trends will give you a competitive edge.</p>',
                'meta_title' => 'Industry Trends 2025 - What to Expect',
                'meta_description' => 'Stay ahead of the curve with our analysis of the top industry trends for 2025.',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'slug' => 'upcoming-features-preview',
                'title' => 'Upcoming Features Preview',
                'excerpt' => 'A sneak peek at the exciting new features we are working on.',
                'content' => '<h1>Upcoming Features Preview</h1><p>We are constantly improving our platform. Here is a preview of some exciting features coming soon.</p><p>We value your feedback and would love to hear what features matter most to you.</p>',
                'meta_title' => 'Upcoming Features Preview',
                'meta_description' => 'Get a sneak peek at the new features we are building for you.',
                'is_published' => false,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );
        }
    }

    /**
     * Seed sample top offers.
     */
    private function seedTopOffers(): void
    {
        $offers = [
            [
                'logo_url' => '/storage/offers/offer-1-logo.png',
                'bonus_text' => 'Get 100% Welcome Bonus up to $500',
                'cta_text' => 'Claim Bonus',
                'target_url' => 'https://example.com/offer-1',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'logo_url' => '/storage/offers/offer-2-logo.png',
                'bonus_text' => '50 Free Spins - No Deposit Required',
                'cta_text' => 'Get Free Spins',
                'target_url' => 'https://example.com/offer-2',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'logo_url' => '/storage/offers/offer-3-logo.png',
                'bonus_text' => 'Exclusive VIP Program with Weekly Cashback',
                'cta_text' => 'Join Now',
                'target_url' => 'https://example.com/offer-3',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($offers as $offer) {
            TopOffer::create($offer);
        }
    }

    /**
     * Seed sample redirects.
     */
    private function seedRedirects(): void
    {
        $redirects = [
            [
                'slug' => 'go/offer-1',
                'target_url' => 'https://example.com/offer-1?ref=site',
                'click_count' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'go/offer-2',
                'target_url' => 'https://example.com/offer-2?ref=site',
                'click_count' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'go/offer-3',
                'target_url' => 'https://example.com/offer-3?ref=site',
                'click_count' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'go/special',
                'target_url' => 'https://example.com/special-promo?ref=site',
                'click_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($redirects as $redirect) {
            Redirect::updateOrCreate(
                ['slug' => $redirect['slug']],
                $redirect
            );
        }
    }
}
