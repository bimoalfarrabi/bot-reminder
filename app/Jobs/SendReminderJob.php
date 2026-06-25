<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReminderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly int $reminderId,
    ) {}

    public function handle(): void
    {
        $reminder = Reminder::find($this->reminderId);

        if (!$reminder || !$reminder->is_active) {
            return;
        }

        $telegram = new TelegramService();
        $telegram->forwardContent($reminder->toArray());

        if ($reminder->is_recurring && $reminder->recurring_pattern) {
            // Reschedule next occurrence
            $parser = new \App\Services\ReminderParser();
            $next = $parser->parse($reminder->recurring_pattern);

            if ($next) {
                $reminder->update([
                    'scheduled_at' => $next,
                    'triggered_at' => null,
                ]);
                return;
            }
        }

        // Mark as triggered (non-recurring or no next occurrence)
        $reminder->update(['triggered_at' => now()]);
    }
}

