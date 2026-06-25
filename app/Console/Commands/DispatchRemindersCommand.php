<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Services\ReminderParser;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class DispatchRemindersCommand extends Command
{
    protected $signature = 'reminders:dispatch';
    protected $description = 'Send due reminders immediately';

    public function handle(): void
    {
        $due = Reminder::due()->get();

        foreach ($due as $reminder) {
            try {
                // Lock: mark triggered immediately to prevent duplicate send
                $reminder->update(['triggered_at' => now()]);

                $telegram = new TelegramService();
                $telegram->forwardContent($reminder->toArray());

                if ($reminder->is_recurring && $reminder->recurring_pattern) {
                    $parser = new ReminderParser();
                    $next = $parser->parse($reminder->recurring_pattern);

                    if ($next) {
                        $reminder->update([
                            'scheduled_at' => $next,
                            'triggered_at' => null,
                        ]);
                        continue;
                    }
                }

                $this->info("Sent reminder #{$reminder->id}.");
            } catch (\Throwable $e) {
                logger()->error("Failed to send reminder #{$reminder->id}: {$e->getMessage()}");
                // Reset triggered_at so next run retries
                $reminder->update(['triggered_at' => null]);
            }
        }

        if ($due->count() > 0) {
            $this->info("Processed {$due->count()} reminder(s).");
        }
    }
}

