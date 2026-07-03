<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(Task $task)
    {
        return response()->json($task->load('categories'));
    }

    public function getCategories()
    {
        return response()->json(\App\Models\Category::select('id', 'value', 'color')->get());
    }

    public function update(Request $request, Task $task)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'fragments' => 'nullable',
            'file' => 'nullable|string',
            'hidden' => 'nullable|boolean',
        ]);

        $fragments = $data['fragments'] ?? [];
        if (!is_array($fragments)) {
            $fragments = [];
        }

        $task->update([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'content' => json_encode($fragments),
            'file_path' => $data['file'] ?? null,
            'hidden' => $data['hidden'] ?? false,
        ]);

        $task->categories()->sync([$data['category_id']]);

        return response()->json($task->load('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'fragments' => 'nullable',
            'file' => 'nullable|string',
            'hidden' => 'nullable|boolean',
        ]);

        $fragments = $data['fragments'] ?? [];
        if (!is_array($fragments)) {
            $fragments = [];
        }

        $task = Task::create([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'content' => json_encode($fragments),
            'file_path' => $data['file'] ?? null,
            'hidden' => $data['hidden'] ?? false,
        ]);

        $task->categories()->attach($data['category_id']);

        return response()->json($task->load('categories'));
    }

    public function getTasks(Request $request)
    {
        $perPage = (int) $request->query('per_page', 21);
        $perPage = max(1, min($perPage, 60));

        $search = trim((string) $request->query('query', ''));
        $taskSecret = config('app.task_secret', env('TASK_SECRET'));

        $tasks = Task::query()
            ->with('categories')
            ->latest();

        if (!$taskSecret || $request->query('secret') !== $taskSecret) {
            $tasks->where('hidden', false);
        }

        if ($search !== '') {
            $tasks->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('value', 'like', "%{$search}%");
                    });
            });
        }

        return $tasks
            ->paginate($perPage)
            ->withQueryString();
    }

    public function destroy(Task $task)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
