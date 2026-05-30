<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_tasks',
        'completed_tasks',
        'focus_sessions',
        'total_focus_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public static function getTodayStats(): self
    {
        return static::firstOrCreate([
            'date' => today(),
        ]);
    }
}
