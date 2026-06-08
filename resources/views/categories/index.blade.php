<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Categorías" eyebrow="Organiza tus ingresos y gastos.">
            <x-slot:action>
                <a href="{{ route('categories.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nueva categoría
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @php
                $typeBuckets = [
                    'income'  => ['label' => 'Ingresos',  'icon' => 'income',  'tone' => 'income',  'toneDot' => 'bg-income-500'],
                    'expense' => ['label' => 'Gastos',    'icon' => 'expense', 'tone' => 'expense', 'toneDot' => 'bg-expense-500'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($typeBuckets as $typeKey => $info)
                    @php $list = $categories->where('type', $typeKey); @endphp
                    <x-ui.card padding="p-0" class="overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3 bg-slate-50/60 dark:bg-slate-800/30">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <span class="w-2.5 h-2.5 rounded-full {{ $info['toneDot'] }}"></span>
                            </div>
                            <div>
                                <h3 class="section-title">{{ $info['label'] }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $list->count() }} {{ \Illuminate\Support\Str::plural('categoría', $list->count()) }}</p>
                            </div>
                        </div>
                        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($list as $category)
                                <li class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-lg bg-{{ $info['tone'] }}-50 dark:bg-{{ $info['tone'] }}-950/40 flex items-center justify-center text-{{ $info['tone'] }}-600 dark:text-{{ $info['tone'] }}-400 flex-shrink-0">
                                            <x-icon :name="$info['icon']" class="w-4 h-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $category->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $category->transactions_count }} {{ \Illuminate\Support\Str::plural('transacción', $category->transactions_count) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <a href="{{ route('categories.edit', $category) }}" class="btn-icon" title="Editar" aria-label="Editar categoría">
                                            <x-icon.edit class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                              onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30" title="Eliminar" aria-label="Eliminar categoría">
                                                <x-icon.trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="p-0">
                                    <x-ui.empty-state
                                        icon="empty-box"
                                        :title="'Sin categorías de ' . strtolower($info['label'])"
                                        description="Crea la primera para empezar a clasificar tus movimientos">
                                        <x-slot:action>
                                            <a href="{{ route('categories.create') }}" class="btn-soft btn-sm">
                                                <x-icon.plus class="w-3.5 h-3.5" /> Crear la primera
                                            </a>
                                        </x-slot:action>
                                    </x-ui.empty-state>
                                </li>
                            @endforelse
                        </ul>
                    </x-ui.card>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
