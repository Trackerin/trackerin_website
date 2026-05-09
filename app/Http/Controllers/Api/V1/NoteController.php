<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Http\Resources\Api\V1\NoteResource;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->notes()->latest();
        if ($request->has('milestone_id')) {
            $query->where('milestone_id', $request->milestone_id);
        }
        return NoteResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'milestone_id' => 'nullable|exists:milestones,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $note = $request->user()->notes()->create($request->all());

        return new NoteResource($note);
    }

    public function show(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new NoteResource($note);
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'milestone_id' => 'nullable|exists:milestones,id',
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string'
        ]);

        $note->update($request->only(['milestone_id', 'title', 'content']));

        return new NoteResource($note);
    }

    public function destroy(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }
}
