<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasRevisions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasRevisions;

    protected $connection = 'tenant';

    protected $table = 'posts';

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'category_id',
        'content_differentiated_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'content_differentiated_at' => 'datetime',
        ];
    }

    /**
     * Scope to only include published posts (is_published = true AND published_at <= now).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to order posts by published_at descending (most recent first).
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }
}
