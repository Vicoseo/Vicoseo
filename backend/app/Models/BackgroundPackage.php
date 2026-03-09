<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackgroundPackage extends Model
{
    protected $connection = 'landlord';

    protected $table = 'background_packages';

    protected $fillable = [
        'name',
        'image_url',
        'site_id',
        'overlay_color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
