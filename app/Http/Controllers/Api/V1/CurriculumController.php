<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Http\Resources\Api\V1\CurriculumResource;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $curriculums = $request->user()->curriculums()->latest()->get();
        return CurriculumResource::collection($curriculums);
    }

    public function show(Request $request, Curriculum $curriculum)
    {
        if ($curriculum->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $curriculum->load(['milestones' => function($q) {
            $q->orderBy('order_index', 'asc');
        }]);

        return new CurriculumResource($curriculum);
    }
}
