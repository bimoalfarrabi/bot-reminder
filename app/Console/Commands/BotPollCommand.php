<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use App\Services\ReminderParser;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class BotPollCommand extends Command
{
    protected $signature = 'bot:poll';
    protected $description = 'Run Telegram bot in polling mode';

    public function handle(): void
    {
        $this->info('Bot polling started. Press Ctrl+C to stop.');

        $controller = new TelegramController(
            new TelegramService(),
            new ReminderParser(),
        );

        $controller->poll();
    }
}

