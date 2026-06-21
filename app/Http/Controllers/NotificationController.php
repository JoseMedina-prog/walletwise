<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all'); // all | unread

        $query = $user->notifications()->latest();
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();

        $counts = Cache::remember(
            "user:{$user->id}:notification_counts",
            30,
            fn () => [
                'all'    => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
            ]
        );

        return view('notifications.index', compact('notifications', 'counts', 'filter'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        $this->invalidateCache();

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->invalidateCache();

        return back()->with('status', 'Todas las notificaciones marcadas como leídas.');
    }

    private function invalidateCache(): void
    {
        Cache::forget("user:" . Auth::id() . ":notification_counts");
        Cache::forget("user:" . Auth::id() . ":unread_notifications_count");
    }
}