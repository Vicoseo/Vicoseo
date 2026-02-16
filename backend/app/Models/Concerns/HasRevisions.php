<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Revision;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRevisions
{
    protected static array $revisionableFields = ['title', 'content', 'meta_title', 'meta_description'];

    public static function bootHasRevisions(): void
    {
        static::updating(function ($model) {
            $userId = auth()->id();

            foreach (static::$revisionableFields as $field) {
                if ($model->isDirty($field)) {
                    Revision::create([
                        'revisionable_type' => get_class($model),
                        'revisionable_id' => $model->id,
                        'field_name' => $field,
                        'old_value' => $model->getOriginal($field),
                        'new_value' => $model->getAttribute($field),
                        'user_id' => $userId,
                        'created_at' => now(),
                    ]);
                }
            }
        });
    }

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable');
    }

    public function revertTo(int $revisionId): bool
    {
        $revision = $this->revisions()
            ->where('id', $revisionId)
            ->firstOrFail();

        $this->update([
            $revision->field_name => $revision->old_value,
        ]);

        return true;
    }
}
