<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Settings/Index', [
            'botToken' => Setting::get('telegram_bot_token') ?? '',
            'chatId'   => Setting::get('telegram_chat_id') ?? '',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'bot_token' => ['required', 'string', 'max:255'],
            'chat_id'   => ['required', 'string', 'max:255'],
        ]);

        Setting::set('telegram_bot_token', $request->bot_token);
        Setting::set('telegram_chat_id', $request->chat_id);

        return back()->with('status', 'settings-updated');
    }
}
