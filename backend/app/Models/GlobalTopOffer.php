<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GlobalTopOffer extends Model
{
    protected $connection = 'landlord';

    protected $table = 'global_top_offers';

    protected $fillable = [
        'slug',
        'logo_url',
        'bonus_text',
        'cta_text',
        'target_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Apply a default ordering by sort_order ascending.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $query): void {
            $query->orderBy('sort_order', 'asc');
        });
    }
}
