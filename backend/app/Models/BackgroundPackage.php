<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundPackage extends Model
{
    protected $connection = 'landlord';

    protected $table = 'background_packages';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'thumbnail_url',
        'overlay_color',
        'overlay_blend',
        'tags',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
