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

            // Add notifications if marked as completed
            if ($isCompleted) {
                $request->user()->notifications()->create([
                    'title' => 'Milestone Selesai! 🎉',
                    'message' => 'Selamat! Kamu telah menyelesaikan milestone "' . $milestone->title . '" dalam kurikulum "' . $curriculum->topic . '".',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);

                if ($progress == 100) {
                    $request->user()->notifications()->create([
                        'title' => 'Roadmap Selesai! 🏆',
                        'message' => 'Luar biasa! Kamu telah menyelesaikan seluruh roadmap belajar "' . $curriculum->topic . '". Terus tingkatkan kemampuanmu!',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }
            }
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

        // Enforce that previous milestones must be completed first
        $uncompletedPrevMilestone = $milestone->curriculum->milestones()
            ->where('order_index', '<', $milestone->order_index)
            ->where('is_completed', false)
            ->exists();

        if ($uncompletedPrevMilestone) {
            return response()->json([
                'message' => 'Selesaikan milestone sebelumnya terlebih dahulu!'
            ], 422);
        }

        // Return the existing quiz if it already exists to avoid duplicate generation/error
        $existingQuiz = $milestone->quizzes()->with('quizQuestions')->latest()->first();
        if ($existingQuiz) {
            return response()->json([
                'message' => 'Kuis berhasil diambil',
                'data' => new \App\Http\Resources\Api\V1\QuizResource($existingQuiz)
            ], 200);
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

    public function submitQuiz(Request $request, Milestone $milestone)
    {
        if ($milestone->curriculum->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'score' => 'required|integer|min:0'
        ]);

        $quiz = $milestone->quizzes()->latest()->first();

        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found for this milestone'], 404);
        }

        $quiz->update([
            'score' => $request->score,
            'is_passed' => $request->score >= 2,
        ]);

        return response()->json([
            'message' => 'Quiz score submitted successfully',
            'data' => new \App\Http\Resources\Api\V1\QuizResource($quiz)
        ]);
    }
}
