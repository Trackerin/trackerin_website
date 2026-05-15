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

    public function generateQuiz(Request $request, Milestone $milestone, \App\Services\AiQuizService $aiQuizService)
    {
        if ($milestone->curriculum->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent generating a new quiz if there's an unfinished one for this milestone
        $existingQuiz = $milestone->quizzes()->latest()->first();
        if ($existingQuiz && is_null($existingQuiz->score)) {
            return response()->json([
                'message' => 'Anda harus mengerjakan kuis yang sudah digenerate sebelumnya sebelum bisa men-generate ulang kuis baru.'
            ], 422);
        }

        $generatedData = $aiQuizService->generateQuiz($milestone);

        if (!$generatedData) {
            return response()->json(['message' => 'Gagal menghasilkan kuis dari AI. Silakan coba lagi.'], 500);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Hapus kuis lama jika ada
            $milestone->quizzes()->delete();

            $quiz = $milestone->quizzes()->create([
                'title' => $generatedData['title'] ?? 'Kuis: ' . $milestone->title,
                'score' => null,
                'is_passed' => null,
            ]);

            $questions = $generatedData['questions'] ?? [];
            foreach ($questions as $qData) {
                $quiz->quizQuestions()->create([
                    'question' => $qData['question'],
                    'options' => $qData['options'],
                    'correct_answer' => $qData['correct_answer'],
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            $quiz->load('quizQuestions');

            return response()->json([
                'message' => 'Kuis berhasil dibuat',
                'data' => new \App\Http\Resources\Api\V1\QuizResource($quiz)
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan kuis: ' . $e->getMessage()], 500);
        }
    }
}
