<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['curriculum_id', 'title', 'order_index', 'is_completed', 'completed_at'])]
class Milestone extends Model
{
    //
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
