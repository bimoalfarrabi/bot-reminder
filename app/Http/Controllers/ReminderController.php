<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReminderController extends Controller
{
    public function index(Request $request): Response
    {
        $sortable = ['id', 'message', 'scheduled_at', 'triggered_at'];
        $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'scheduled_at';
        $dir  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $reminders = Reminder::orderBy($sort, $dir)->paginate(20)->withQueryString();

        return Inertia::render('Reminders/Index', [
            'reminders' => $reminders,
            'sort'      => $sort,
            'dir'       => $dir,
        ]);
    }

    public function destroy(Reminder $reminder): RedirectResponse
    {
        $reminder->update(['is_active' => false]);

        return back()->with('success', "Reminder #{$reminder->id} dihentikan.");
    }
}
