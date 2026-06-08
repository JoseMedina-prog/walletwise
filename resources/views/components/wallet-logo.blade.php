@props(['compact' => false, 'color' => 'currentColor'])

<a href="/" class="inline-flex items-center gap-2.5 {{ $attributes->get('class') }}">
    <x-wallet-mark class="w-9 h-9" />
    <span class="text-xl font-bold tracking-tight {{ $color === 'white' ? 'text-white' : 'text-gray-900 dark:text-white' }}">
        Wallet<span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Wise</span>
    </span>
</a>
