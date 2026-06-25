<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReminderController extends Controller
{
    public function index(): Response
    {
        $reminders = Reminder::orderByDesc('scheduled_at')->paginate(20);

        return Inertia::render('Reminders/Index', [
            'reminders' => $reminders,
        ]);
    }

    public function destroy(Reminder $reminder): RedirectResponse
    {
        $reminder->update(['is_active' => false]);

        return back()->with('success', "Reminder #{$reminder->id} dihentikan.");
    }
}
