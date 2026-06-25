<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotState extends Model
{
    protected $fillable = [
        'chat_id',
        'state',
        'reminder_data',
    ];

    protected $casts = [
        'reminder_data' => 'array',
    ];

    public static function getOrCreate(int $chatId): self
    {
        return self::firstOrCreate(['chat_id' => $chatId], ['state' => '']);
    }

    public static function clear(int $chatId): void
    {
        self::where('chat_id', $chatId)->delete();
    }
}

