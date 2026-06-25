<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $token;
    private string $baseUrl;

    public function __construct()
    {
        $token = Setting::get('telegram_bot_token');

        if (!$token) {
            throw new \RuntimeException('Telegram bot token not configured');
        }

        $this->token = $token;
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function sendMessage(int $chatId, string $text, array $options = []): array
    {
        return $this->request('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options));
    }

    public function sendPhoto(int $chatId, string $fileId, ?string $caption = null): array
    {
        return $this->request('sendPhoto', array_filter([
            'chat_id' => $chatId,
            'photo' => $fileId,
            'caption' => $caption,
        ]));
    }

    public function sendDocument(int $chatId, string $fileId, ?string $caption = null): array
    {
        return $this->request('sendDocument', array_filter([
            'chat_id' => $chatId,
            'document' => $fileId,
            'caption' => $caption,
        ]));
    }

    public function sendVideo(int $chatId, string $fileId, ?string $caption = null): array
    {
        return $this->request('sendVideo', array_filter([
            'chat_id' => $chatId,
            'video' => $fileId,
            'caption' => $caption,
        ]));
    }

    public function forwardContent(array $reminder): void
    {
        $chatId = (int) $reminder['chat_id'];
        $caption = '📌 Kamu pernah menyimpan ini.';

        match ($reminder['content_type']) {
            'text'     => $this->sendMessage($chatId, $reminder['content'] . "\n\n" . $caption),
            'photo'    => $this->sendPhoto($chatId, $reminder['file_id'], $reminder['content'] ?? $caption),
            'document' => $this->sendDocument($chatId, $reminder['file_id'], $reminder['content'] ?? $caption),
            'video'    => $this->sendVideo($chatId, $reminder['file_id'], $reminder['content'] ?? $caption),
        };
    }

    public function getUpdates(int $offset = 0): array
    {
        $response = $this->request('getUpdates', [
            'offset' => $offset,
            'timeout' => 10,
        ]);

        return $response['result'] ?? [];
    }

    private function request(string $method, array $params = []): array
    {
        $response = Http::post("{$this->baseUrl}/{$method}", $params);

        return $response->json() ?? [];
    }
}
