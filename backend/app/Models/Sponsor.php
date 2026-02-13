<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $connection = 'landlord';

    protected $table = 'sponsors';

    protected $fillable = [
        'name',
        'url',
        'primary_color',
        'web_background',
        'mobile_background',
        'logo',
        'rating',
        'promotions',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'promotions' => 'array',
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
