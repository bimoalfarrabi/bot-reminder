<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'chat_id',
        'content_type',
        'content',
        'file_id',
        'scheduled_at',
        'is_recurring',
        'recurring_pattern',
        'is_active',
        'triggered_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'triggered_at' => 'datetime',
        'is_recurring'  => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('scheduled_at', '>', now())
            ->where('is_active', true)
            ->whereNull('triggered_at');
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('scheduled_at', '<=', now())
            ->where('is_active', true)
            ->whereNull('triggered_at');
    }
}

