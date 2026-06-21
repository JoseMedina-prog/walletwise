<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Notificaciones" eyebrow="Alertas y avisos del sistema.">
            <x-slot:action>
                @if ($counts['unread'] > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            Marcar todas como leídas
                        </button>
                    </form>
                @endif
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif

            {{-- Filter tabs --}}
            <div class="inline-flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                   @class([
                       'inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-semibold transition',
                       'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' => $filter !== 'unread',
                       'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => $filter === 'unread',
                   ])>
                    Todas
                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                   @class([
                       'inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-semibold transition',
                       'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' => $filter === 'unread',
                       'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => $filter !== 'unread',
                   ])>
                    No leídas
                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded text-[10px] font-bold bg-expense-100 dark:bg-expense-950/60 text-expense-700 dark:text-expense-300">{{ $counts['unread'] }}</span>
                </a>
            </div>

            {{-- List --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                @forelse ($notifications as $n)
                    @php $data = $n->data; @endphp
                    <div @class([
                        'px-5 sm:px-6 py-4 transition',
                        'border-b border-slate-100 dark:border-slate-800' => !$loop->last,
                        'bg-brand-50/50 dark:bg-brand-950/20' => $n->read_at === null,
                    ])>
                        <div class="flex items-start gap-4">
                            <div @class([
                                'w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0',
                                'bg-expense-100 dark:bg-expense-950/50 text-expense-600 dark:text-expense-400' => ($data['level'] ?? null) === 'over',
                                'bg-accent-100 dark:bg-accent-950/50 text-accent-600 dark:text-accent-400' => ($data['level'] ?? null) === 'warn',
                                'bg-slate-100 dark:bg-slate-800 text-slate-500' => !in_array(($data['level'] ?? null), ['over', 'warn'], true),
                            ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                                            {{ $data['title'] ?? 'Notificación' }}
                                        </h4>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">
                                            {{ $data['body'] ?? '' }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 dark:text-slate-400">
                                            <span class="num">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                                            @if (isset($data['budget_id']))
                                                <span>·</span>
                                                <a href="{{ route('budgets.index') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline">Ver presupuesto</a>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($n->read_at === null)
                                        <form method="POST" action="{{ route('notifications.mark-as-read', $n->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline"
                                                    title="Marcar como leída">
                                                Marcar leída
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state
                        icon="empty-box"
                        title="No tienes notificaciones"
                        :description="$filter === 'unread' ? 'Cuando recibas alertas nuevas aparecerán aquí.' : 'Aún no se ha generado ninguna notificación.'" />
                @endforelse

                @if ($notifications->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>