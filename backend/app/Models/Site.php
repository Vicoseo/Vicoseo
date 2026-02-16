<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $connection = 'landlord';

    protected $table = 'sites';

    protected $fillable = [
        'domain',
        'name',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'db_name',
        'is_active',
        'meta_title',
        'meta_description',
        'social_links',
        'ga_measurement_id',
        'entry_url',
        'login_url',
        'show_sponsors',
        'sponsor_page_visible',
        'sponsor_contact_url',
        'sponsor_contact_text',
        'fallback_domain',
        'robots_txt',
        'noindex_mode',
        'content_prompt_template',
        'custom_css',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_sponsors' => 'boolean',
            'sponsor_page_visible' => 'boolean',
            'noindex_mode' => 'boolean',
            'social_links' => 'array',
        ];
    }
}
