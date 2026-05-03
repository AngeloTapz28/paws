<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->systemNotifications()
            ->latest()
            ->paginate(20);

        // Mark all as read when viewing the list
        auth()->user()->systemNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(int $id): RedirectResponse
    {
        $notif = SystemNotification::where('user_id', auth()->id())->findOrFail($id);
        $notif->markAsRead();
        return redirect()->back();
    }

    public function readAll(): RedirectResponse
    {
        auth()->user()->systemNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}