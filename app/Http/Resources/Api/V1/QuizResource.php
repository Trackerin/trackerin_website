<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'milestone_id' => $this->milestone_id,
            'title' => $this->title,
            'score' => $this->score,
            'is_passed' => (bool) $this->is_passed,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'questions' => QuizQuestionResource::collection($this->whenLoaded('quizQuestions')),
        ];
    }
}
