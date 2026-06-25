<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BotState;
use App\Models\Reminder;
use App\Services\ReminderParser;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegram,
        private readonly ReminderParser $parser,
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        try {
            $update = $request->json()->all();

            $message = $update['message'] ?? $update['edited_message'] ?? null;

            if ($message) {
                $this->handleMessage($message);
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            logger()->error('Webhook error: ' . $e->getMessage());
            return response()->json(['ok' => false], 500);
        }
    }

    public function poll(): void
    {
        $offset = 0;

        while (true) {
            try {
                $updates = $this->telegram->getUpdates($offset);

                foreach ($updates as $update) {
                    $message = $update['message'] ?? $update['edited_message'] ?? null;
                    if ($message) {
                        $this->handleMessage($message);
                    }
                    $offset = $update['update_id'] + 1;
                }
            } catch (\Exception $e) {
                logger()->error('Bot poll error: ' . $e->getMessage());
                sleep(5);
            }

            sleep(1);
        }
    }

    private function handleMessage(array $message): void
    {
        $chatId = (int) ($message['chat']['id'] ?? 0);
        if (!$chatId) return;

        // Whitelist: hanya izinkan chat ID yang terdaftar di settings
        $allowedChatId = (int) \App\Models\Setting::get('telegram_chat_id');
        if ($allowedChatId && $chatId !== $allowedChatId) {
            return; // Abaikan pesan dari chat lain
        }

        // Whitelist user ID: tolak jika user ID tidak diizinkan
        $allowedUserIds = \App\Models\Setting::get('telegram_allowed_user_ids') ?? '';
        if ($allowedUserIds !== '') {
            $userId = (int) ($message['from']['id'] ?? 0);
            $allowed = array_map('intval', array_filter(array_map('trim', explode(',', $allowedUserIds))));
            if (!in_array($userId, $allowed, true)) {
                return;
            }
        }

        // Strip @botname suffix from commands (e.g. /help@viasco_reminder_bot → /help)
        $rawText = $message['text'] ?? null;
        $text = $rawText ? preg_replace('/^(\/\w+)@\w+/', '$1', $rawText) : null;

        // Commands always take priority over active state
        if ($text === '/cancel') {
            BotState::clear($chatId);
            $this->telegram->sendMessage($chatId, 'Dibatalkan.');
            return;
        }

        if ($text === '/start' || $text === '/help') {
            $this->telegram->sendMessage($chatId,
                "👋 <b>Bot Reminder</b> — Panduan Penggunaan\n\n" .
                "Kirim pesan, foto, atau file ke saya → saya tanya kapan → isi format waktu → selesai!\n\n" .
                "📋 <b>Commands:</b>\n" .
                "/help — tampilkan panduan ini\n" .
                "/list — lihat reminder upcoming (maks 10)\n" .
                "/stop {id} — hentikan reminder, contoh: /stop 5\n" .
                "/cancel — batalkan input yang sedang berjalan\n\n" .
                "⏰ <b>Format waktu:</b>\n" .
                "besok, 2PM\n" .
                "hari ini, 3:30PM\n" .
                "3 hari lagi, 09:00\n" .
                "setiap Senin, 9AM\n" .
                "setiap tanggal 1, 08:00"
            );
            return;
        }

        $state = BotState::getOrCreate($chatId);

        // State: awaiting interval input
        if ($state->state === 'awaiting_interval') {
            if (!$text) {
                $this->telegram->sendMessage($chatId, 'Format tidak dikenali. Contoh: besok, 2PM atau setiap Senin, 9AM');
                return;
            }

            $scheduled = $this->parser->parse($text);

            if (!$scheduled) {
                $this->telegram->sendMessage($chatId, 'Format tidak dikenali. Contoh: besok, 2PM atau setiap Senin, 9AM');
                return;
            }

            $data = $state->reminder_data ?? [];
            $isRecurring = str_contains(strtolower($text), 'setiap');

            Reminder::create([
                'chat_id'          => $chatId,
                'content_type'     => $data['content_type'] ?? 'text',
                'content'          => $data['content'] ?? null,
                'file_id'          => $data['file_id'] ?? null,
                'scheduled_at'     => $scheduled,
                'is_recurring'     => $isRecurring,
                'recurring_pattern' => $isRecurring ? $text : null,
            ]);

            BotState::clear($chatId);

            $localTime = $scheduled->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';
            $this->telegram->sendMessage($chatId, "Oke, saya ingatkan pada {$localTime}.");
            return;
        }

        // No active state — handle commands and new messages
        if ($text === '/list') {
            $reminders = Reminder::upcoming()->where('chat_id', $chatId)->orderBy('scheduled_at')->limit(10)->get();

            if ($reminders->isEmpty()) {
                $this->telegram->sendMessage($chatId, 'Tidak ada reminder upcoming.');
                return;
            }

            $lines = $reminders->map(function (Reminder $r): string {
                $time = $r->scheduled_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';
                $type = $r->content_type === 'text' ? ($r->content ?? '-') : "[{$r->content_type}]";
                $recurring = $r->is_recurring ? ' 🔁' : '';
                return "#{$r->id} {$time}{$recurring}\n  " . mb_strimwidth($type, 0, 50, '...');
            });

            $this->telegram->sendMessage($chatId, "📋 Reminder upcoming:\n\n" . $lines->implode("\n\n") . "\n\nGunakan /stop {id} untuk menghentikan.");
            return;
        }

        if ($text && preg_match('/^\/stop\s+(\d+)$/i', $text, $m)) {
            $reminder = Reminder::where('id', (int)$m[1])->where('chat_id', $chatId)->first();

            if (!$reminder) {
                $this->telegram->sendMessage($chatId, "Reminder #{$m[1]} tidak ditemukan.");
                return;
            }

            $reminder->update(['is_active' => false]);
            $this->telegram->sendMessage($chatId, "✅ Reminder #{$reminder->id} dihentikan.");
            return;
        }

        if ($text === '/cancel') {
            BotState::clear($chatId);
            $this->telegram->sendMessage($chatId, 'Dibatalkan.');
            return;
        }

        // New content — save to BotState and ask for interval
        $data = $this->extractMessageData($message);
        $state->update(['state' => 'awaiting_interval', 'reminder_data' => $data]);

        $this->telegram->sendMessage($chatId,
            'Simpan! Kapan saya ingatkan? (contoh: besok, 2PM atau 3 hari lagi, 09:00)'
        );
    }

    private function extractMessageData(array $message): array
    {
        if (isset($message['photo'])) {
            $photo = end($message['photo']);
            return [
                'content_type' => 'photo',
                'file_id'      => $photo['file_id'],
                'content'      => $message['caption'] ?? null,
            ];
        }

        if (isset($message['document'])) {
            return [
                'content_type' => 'document',
                'file_id'      => $message['document']['file_id'],
                'content'      => $message['caption'] ?? null,
            ];
        }

        if (isset($message['video'])) {
            return [
                'content_type' => 'video',
                'file_id'      => $message['video']['file_id'],
                'content'      => $message['caption'] ?? null,
            ];
        }

        return [
            'content_type' => 'text',
            'content'      => $message['text'] ?? '',
            'file_id'      => null,
        ];
    }
}

