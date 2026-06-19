<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $options = $this->options;
        if (is_array($options) && count($options) > 4) {
            $filtered = array_values(array_filter($options, function ($opt) {
                $trimmed = strtoupper(trim($opt));
                return !in_array($trimmed, ['A', 'B', 'C', 'D', 'A.', 'B.', 'C.', 'D.']);
            }));
            if (count($filtered) >= 4) {
                $options = $filtered;
            }
        }

        return [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'question' => $this->question,
            'options' => $options,
            'correct_answer' => $this->correct_answer,
        ];
    }
}
