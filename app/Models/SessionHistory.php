<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'started_at',
        'ended_at',
        'duration',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'completed' => 'boolean',
        ];
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeFocus($query)
    {
        return $query->where('type', 'focus');
    }
}
