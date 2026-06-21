@php
    $user = auth()->user();
    $unreadCount = $user
        ? \Illuminate\Support\Facades\Cache::remember(
            "user:{$user->id}:unread_notifications_count",
            30,
            fn () => $user->unreadNotifications()->count()
          )
        : 0;
@endphp

<a href="{{ route('notifications.index') }}"
   class="relative btn-icon"
   :class="''"
   aria-label="Notificaciones"
   title="Notificaciones">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>

    @if ($unreadCount > 0)
        <span class="absolute -top-0.5 -end-0.5 min-w-[1.1rem] h-[1.1rem] px-1 inline-flex items-center justify-center rounded-full bg-expense-500 text-white text-[10px] font-bold ring-2 ring-white dark:ring-slate-900">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
    @endif
</a>