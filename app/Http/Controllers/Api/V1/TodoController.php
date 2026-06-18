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
            'due_date' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $d1 = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    $d2 = \DateTime::createFromFormat('Y-m-d', $value);
                    $isoRegex = '/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:?\d{2})?$/';
                    if (
                        ($d1 && $d1->format('Y-m-d H:i:s') === $value) ||
                        ($d2 && $d2->format('Y-m-d') === $value) ||
                        preg_match($isoRegex, $value)
                    ) {
                        return;
                    }
                    $fail('Format due_date tidak valid. Harus berupa format tanggal yang valid (misal: Y-m-d, Y-m-d H:i:s, atau ISO 8601).');
                }
            ],
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
            'due_date' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $d1 = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    $d2 = \DateTime::createFromFormat('Y-m-d', $value);
                    $isoRegex = '/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:?\d{2})?$/';
                    if (
                        ($d1 && $d1->format('Y-m-d H:i:s') === $value) ||
                        ($d2 && $d2->format('Y-m-d') === $value) ||
                        preg_match($isoRegex, $value)
                    ) {
                        return;
                    }
                    $fail('Format due_date tidak valid. Harus berupa format tanggal yang valid (misal: Y-m-d, Y-m-d H:i:s, atau ISO 8601).');
                }
            ],
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
