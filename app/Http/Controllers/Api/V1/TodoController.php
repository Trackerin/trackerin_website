<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Http\Resources\Api\V1\TodoResource;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $todos = $request->user()->todos()->latest()->get();
        return TodoResource::collection($todos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'is_done' => 'boolean'
        ]);

        $todo = $request->user()->todos()->create([
            'task' => $request->task,
            'due_date' => $request->due_date,
            'is_done' => $request->is_done ?? false,
        ]);

        return new TodoResource($todo);
    }

    public function show(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new TodoResource($todo);
    }

    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'task' => 'sometimes|required|string|max:255',
            'due_date' => 'nullable|date',
            'is_done' => 'sometimes|boolean'
        ]);

        $todo->update($request->only(['task', 'due_date', 'is_done']));

        return new TodoResource($todo);
    }

    public function destroy(Request $request, Todo $todo)
    {
        if ($todo->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $todo->delete();
        return response()->json(['message' => 'Todo deleted successfully']);
    }
}
