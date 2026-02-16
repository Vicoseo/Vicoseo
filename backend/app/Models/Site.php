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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_sponsors' => 'boolean',
            'social_links' => 'array',
        ];
    }
}
