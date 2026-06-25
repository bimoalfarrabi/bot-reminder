<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendReminderJob;
use App\Models\Reminder;
use Illuminate\Console\Command;

class DispatchRemindersCommand extends Command
{
    protected $signature = 'reminders:dispatch';
    protected $description = 'Dispatch due reminders to the queue';

    public function handle(): void
    {
        $due = Reminder::due()->get();

        foreach ($due as $reminder) {
            // Mark dispatched immediately to prevent duplicate dispatch
            $reminder->update(['triggered_at' => now()]);
            SendReminderJob::dispatch($reminder->id);
        }

        if ($due->count() > 0) {
            $this->info("Dispatched {$due->count()} reminder(s).");
        }
    }
}

