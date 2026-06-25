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
        $update = $request->json()->all();
        $message = $update['message'] ?? $update['edited_message'] ?? null;

        if ($message) {
            $this->handleMessage($message);
        }

        return response()->json(['ok' => true]);
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

        $text = $message['text'] ?? null;
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
        if ($text === '/start') {
            $this->telegram->sendMessage($chatId,
                "👋 Halo! Kirim pesan, foto, atau file ke saya, lalu saya tanya kapan kamu mau diingatkan.\n\n" .
                "Contoh format waktu:\n" .
                "• besok, 9AM\n• 3 hari lagi, 15:00\n• setiap Senin, 9AM\n\n" .
                "Perintah:\n/list — lihat reminder\n/cancel — batalkan input"
            );
            return;
        }

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

