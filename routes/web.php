<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TelegramController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Telegram webhook — no auth middleware
Route::post('/webhook/telegram', [TelegramController::class, 'webhook'])->name('webhook.telegram');

// DEBUG ONLY — remove after fix
Route::get('/debug-settings', function () {
    $chatId = \App\Models\Setting::get('telegram_chat_id');
    $token = \App\Models\Setting::get('telegram_bot_token');

    return response()->json([
        'chat_id' => $chatId,
        'allowed_user_ids' => \App\Models\Setting::get('telegram_allowed_user_ids'),
        'token_length' => strlen($token ?? ''),
    ]);
});

// DEBUG ONLY — dump last webhook payload
Route::get('/debug-last-update', function () {
    $path = storage_path('logs/last_update.json');
    if (!file_exists($path)) {
        return response()->json(['error' => 'no update received yet']);
    }
    return response()->file($path, ['Content-Type' => 'application/json']);
});

Route::get('/debug-webhook-error', function () {
    $path = storage_path('logs/webhook_error.txt');
    if (!file_exists($path)) {
        return response()->json(['error' => 'no error logged yet']);
    }
    return response($file = file_get_contents($path), 200, ['Content-Type' => 'text/plain']);
});

require __DIR__.'/auth.php';
