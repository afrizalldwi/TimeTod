<?php

namespace App\Http\Controllers;

use App\Models\DailyStat;
use App\Models\SessionHistory;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $tasks = Task::when($filter === 'active', fn($q) => $q->active())
            ->when($filter === 'completed', fn($q) => $q->completed())
            ->latest()
            ->get();

        $stats = [
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::completed()->count(),
            'focus_sessions_today' => SessionHistory::whereDate('created_at', today())
                ->focus()
                ->count(),
            'total_focus_time_today' => SessionHistory::whereDate('created_at', today())
                ->focus()
                ->sum('duration'),
            'streak' => $this->calculateStreak(),
        ];

        $progressPercentage = $stats['total_tasks'] > 0
            ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100)
            : 0;

        return view('dashboard.index', compact(
            'tasks', 'stats', 'filter', 'progressPercentage'
        ));
    }

    private function calculateStreak(): int
    {
        $streak = 0;
        $date = today();

        while (true) {
            $focusCount = SessionHistory::whereDate('created_at', $date)
                ->focus()
                ->count();

            if ($focusCount === 0) {
                if ($date->isToday()) {
                    $date = $date->subDay();
                    continue;
                }
                break;
            }

            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }
}
