<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotTask extends Model
{
    protected $connection = 'landlord';

    protected $table = 'bot_tasks';

    protected $fillable = [
        'type',
        'status',
        'source_site_id',
        'target_site_id',
        'batch_id',
        'payload',
        'result',
        'progress',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'result' => 'array',
            'progress' => 'integer',
        ];
    }

    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'source_site_id');
    }

    public function targetSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'target_site_id');
    }
}
