<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['quiz_id', 'question', 'correct_answer', 'options'])]
class QuizQuestion extends Model
{
    //
    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
