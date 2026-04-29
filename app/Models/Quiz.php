<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['milestone_id', 'title', 'score', 'is_passed'])]
class Quiz extends Model
{
    //
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
