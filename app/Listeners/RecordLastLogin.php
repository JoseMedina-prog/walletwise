<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;

class RecordLastLogin
{
    public function handleLogin(Login $event): void
    {
        $user = $event->user;

        if (! $user || ! method_exists($user, 'markLastLogin')) {
            return;
        }

        // Bloquea cuentas inactivas: el usuario inicia sesión pero se le cierra
        // inmediatamente. Esto evita que Breeze autentique cuentas desactivadas
        // por un administrador.
        if (method_exists($user, 'isActive') && ! $user->isActive()) {
            Auth::guard()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return;
        }

        $user->markLastLogin();
    }

    public function handleLogout(Logout $event): void
    {
        // Hook reservado (p.ej. invalidar tokens, registrar auditoría).
    }

    public function subscribe($events): array
    {
        return [
            Login::class  => 'handleLogin',
            Logout::class => 'handleLogout',
        ];
    }
}
