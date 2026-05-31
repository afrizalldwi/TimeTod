<?php

namespace App\Http\Controllers;

use App\Models\DailyStat;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        Task::create($data);

        DailyStat::getTodayStats()->increment('total_tasks');

        return redirect()->route('dashboard');
    }

    public function toggle(Task $task): JsonResponse
    {
        $wasCompleted = $task->completed;

        $task->update([
            'completed' => !$wasCompleted,
            'completed_at' => $wasCompleted ? null : now(),
        ]);

        $dailyStat = DailyStat::getTodayStats();

        if ($wasCompleted) {
            $dailyStat->decrement('completed_tasks');
        } else {
            $dailyStat->increment('completed_tasks');
        }

        return response()->json(['completed' => $task->completed]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task->update($data);

        return redirect()->route('dashboard');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $dailyStat = DailyStat::getTodayStats();

        if ($task->completed) {
            $dailyStat->decrement('completed_tasks');
        }

        $dailyStat->decrement('total_tasks');

        $task->delete();

        return redirect()->route('dashboard');
    }
}
