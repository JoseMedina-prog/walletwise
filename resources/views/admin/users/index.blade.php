@extends('admin.layouts.admin')

@section('title', 'Usuarios')

@section('content')
    <div class="mb-6">
        <x-ui.section-header title="Usuarios" eyebrow="Gestiona las cuentas de la plataforma. Los datos financieros NO son accesibles desde aquí.">
            <x-slot:action>
                <a href="{{ route('admin.users.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nuevo usuario
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </div>

    @if (session('status'))
        <x-ui.alert type="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif
    @if (session('error'))
        <x-ui.alert type="error" class="mb-5">{{ session('error') }}</x-ui.alert>
    @endif

    <x-ui.card padding="p-4" class="mb-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="q" class="form-label">Buscar</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nombre o correo…"
                       class="form-input">
            </div>
            <div>
                <label for="role" class="form-label">Rol</label>
                <select id="role" name="role" class="form-select">
                    <option value="">Todos</option>
                    <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>Usuarios</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administradores</option>
                </select>
            </div>
            <div>
                <label for="status" class="form-label">Estado</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="sm:col-span-4 flex items-center gap-2">
                <button type="submit" class="btn-primary">
                    <x-icon.filter class="w-4 h-4" /> Aplicar filtros
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Limpiar</a>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-right">Categorías</th>
                        <th class="text-right">Transacciones</th>
                        <th>Último login</th>
                        <th>Registrado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center text-slate-700 dark:text-slate-200 text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $u->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($u->role === 'admin')
                                    <x-ui.badge type="brand">Admin</x-ui.badge>
                                @else
                                    <x-ui.badge type="neutral">Usuario</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                @if ($u->is_active)
                                    <x-ui.badge type="income">Activo</x-ui.badge>
                                @else
                                    <x-ui.badge type="expense">Inactivo</x-ui.badge>
                                @endif
                            </td>
                            <td class="text-right num text-slate-700 dark:text-slate-200">{{ number_format($u->categories_count) }}</td>
                            <td class="text-right num text-slate-700 dark:text-slate-200">{{ number_format($u->transactions_count) }}</td>
                            <td class="text-slate-600 dark:text-slate-300 num text-xs whitespace-nowrap">
                                {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Nunca' }}
                            </td>
                            <td class="text-slate-600 dark:text-slate-300 num text-xs whitespace-nowrap">
                                {{ $u->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn-icon" title="Editar" aria-label="Editar usuario">
                                    <x-icon.edit class="w-4 h-4" />
                                </a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar al usuario {{ $u->name }}? Esta acción es permanente.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30" title="Eliminar" aria-label="Eliminar usuario">
                                            <x-icon.trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <x-ui.empty-state icon="user" title="No hay usuarios con esos filtros"
                                    description="Prueba a limpiar los filtros o crea uno nuevo" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                {{ $users->links() }}
            </div>
        @endif
    </x-ui.card>
@endsection
