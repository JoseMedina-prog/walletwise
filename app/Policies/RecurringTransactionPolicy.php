<?php

namespace App\Policies;

use App\Models\RecurringTransaction;
use App\Models\User;

class RecurringTransactionPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, RecurringTransaction $r): bool { return $user->id === $r->user_id; }
    public function create(User $user): bool { return true; }
    public function update(User $user, RecurringTransaction $r): bool { return $user->id === $r->user_id; }
    public function delete(User $user, RecurringTransaction $r): bool { return $user->id === $r->user_id; }
    public function post(User $user, RecurringTransaction $r): bool { return $user->id === $r->user_id; }
}