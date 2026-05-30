<?php

namespace App\Http\Controllers;

use App\Models\DailyStat;
use App\Models\SessionHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimerController extends Controller
{
    public function complete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:focus,short_break,long_break'],
            'duration' => ['required', 'integer', 'min:1'],
        ]);

        $session = SessionHistory::create([
            'type' => $data['type'],
            'started_at' => now()->subSeconds($data['duration']),
            'ended_at' => now(),
            'duration' => $data['duration'],
            'completed' => true,
        ]);

        if ($data['type'] === 'focus') {
            $dailyStat = DailyStat::getTodayStats();
            $dailyStat->increment('focus_sessions');
            $dailyStat->increment('total_focus_time', $data['duration']);
        }

        return response()->json([
            'message' => 'Session completed',
            'session' => $session,
        ]);
    }

    public function today(): JsonResponse
    {
        $focusSessions = SessionHistory::whereDate('created_at', today())
            ->focus()
            ->count();

        $totalFocusTime = SessionHistory::whereDate('created_at', today())
            ->focus()
            ->sum('duration');

        return response()->json([
            'focus_sessions' => $focusSessions,
            'total_focus_time' => $totalFocusTime,
        ]);
    }
}
