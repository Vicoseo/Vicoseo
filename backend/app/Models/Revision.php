<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Revision extends Model
{
    protected $connection = 'tenant';

    protected $table = 'revisions';

    public $timestamps = false;

    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'field_name',
        'old_value',
        'new_value',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }
}
