<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function sendMessage(string $text, ?string $chatId = null): void
    {
        $token = config('telegram.bot_token');
        $chatId = $chatId ?? config('telegram.default_chat_id');

        if (!$token || !$chatId) {
            return;
        }

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram sendMessage failed: ' . $e->getMessage());
        }
    }
}

