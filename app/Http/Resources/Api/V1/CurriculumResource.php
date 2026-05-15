<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'description' => $this->description,
            'total_progress' => $this->total_progress,
            'is_completed' => (bool) $this->is_completed,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'milestones' => MilestoneResource::collection($this->whenLoaded('milestones')),
        ];
    }
}
