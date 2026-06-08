<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 focus:outline-none focus:bg-slate-50 dark:focus:bg-slate-800/60 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>
