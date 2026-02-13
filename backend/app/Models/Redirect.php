<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $connection = 'tenant';

    protected $table = 'redirects';

    protected $fillable = [
        'slug',
        'target_url',
        'click_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'click_count' => 'integer',
        ];
    }

    /**
     * Atomically increment the click_count for this redirect.
     */
    public function incrementClick(): void
    {
        $this->increment('click_count');
    }
}
