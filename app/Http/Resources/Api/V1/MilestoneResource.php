<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilestoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'curriculum_id' => $this->curriculum_id,
            'title' => $this->title,
            'order_index' => $this->order_index,
            'is_completed' => (bool) $this->is_completed,
            'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'quizzes' => $this->whenLoaded('quizzes'),
        ];
    }
}
