<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Http\Resources\Api\V1\MilestoneResource;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * Mark a milestone as completed/uncompleted and update curriculum progress
     */
    public function updateProgress(Request $request, Milestone $milestone)
    {
        // Check authorization via the parent curriculum
        if ($milestone->curriculum->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_completed' => 'required|boolean'
        ]);

        $isCompleted = $request->is_completed;

        $milestone->update([
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null
        ]);

        // Recalculate curriculum progress
        $curriculum = $milestone->curriculum;
        $totalMilestones = $curriculum->milestones()->count();
        
        if ($totalMilestones > 0) {
            $completedMilestones = $curriculum->milestones()->where('is_completed', true)->count();
            $progress = round(($completedMilestones / $totalMilestones) * 100);
            
            $curriculum->update([
                'total_progress' => $progress,
                'is_completed' => $progress == 100
            ]);
        }

        return response()->json([
            'message' => 'Milestone progress updated successfully',
            'data' => new MilestoneResource($milestone),
            'curriculum_progress' => $curriculum->total_progress
        ]);
    }
}
