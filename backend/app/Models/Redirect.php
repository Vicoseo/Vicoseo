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
        'status_code',
        'description',
        'click_count',
        'is_active',
        'last_clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'click_count' => 'integer',
            'status_code' => 'integer',
            'last_clicked_at' => 'datetime',
        ];
    }

    /**
     * Atomically increment the click_count and update last_clicked_at.
     */
    public function incrementClick(): void
    {
        $this->increment('click_count');
        $this->update(['last_clicked_at' => now()]);
    }
}
