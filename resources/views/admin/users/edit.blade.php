@extends('admin.layouts.admin')

@section('title', 'Editar usuario')

@section('content')
    <div class="mb-6">
        <x-ui.section-header
            :title="'Editar · ' . $user->name"
            :eyebrow="'Cuenta registrada el ' . $user->created_at->format('Y-m-d') . ' · ' . $user->categories_count . ' categorías · ' . $user->transactions_count . ' transacciones'"
            :back="route('admin.users.index')" />
    </div>

    <div class="max-w-2xl">
        <x-ui.card padding="p-7">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-ui.input id="name" name="name" type="text" label="Nombre"
                            :value="old('name', $user->name)" required autofocus
                            :error="$errors->first('name')" />

                <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                            :value="old('email', $user->email)" required
                            :error="$errors->first('email')" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.input id="password" name="password" type="password" label="Nueva contraseña (opcional)"
                                :error="$errors->first('password')" placeholder="Dejar en blanco para no cambiar" />

                    <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                                label="Confirmar contraseña" placeholder="Repite la nueva contraseña" />
                </div>

                <div>
                    <label class="form-label">Rol</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-2 p-3.5 border-2 rounded-xl cursor-pointer transition focus-within:ring-2 focus-within:ring-brand-500/30
                            {{ old('role', $user->role) === 'user' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 bg-white dark:bg-slate-900' }}">
                            <input type="radio" name="role" value="user" {{ old('role', $user->role) === 'user' ? 'checked' : '' }} class="sr-only">
                            <x-icon.user class="w-4 h-4 text-slate-600 dark:text-slate-300" />
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Usuario</span>
                        </label>
                        <label class="relative flex items-center gap-2 p-3.5 border-2 rounded-xl cursor-pointer transition focus-within:ring-2 focus-within:ring-brand-500/30
                            {{ old('role', $user->role) === 'admin' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 bg-white dark:bg-slate-900' }}">
                            <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} class="sr-only">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-slate-600 dark:text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Administrador</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-2 focus:ring-brand-500/30 bg-white dark:bg-slate-900">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Cuenta activa</span>
                </label>

                <div class="flex items-center justify-between gap-3 pt-5 border-t border-slate-200 dark:border-slate-800">
                    @if ($user->id === auth()->id())
                        <span class="text-xs text-slate-500 dark:text-slate-400">No puedes eliminar tu propia cuenta.</span>
                    @else
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('¿Eliminar al usuario {{ $user->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost text-expense-600 dark:text-expense-400">
                                <x-icon.trash class="w-4 h-4" /> Eliminar usuario
                            </button>
                        </form>
                    @endif
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar cambios</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
