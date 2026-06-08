<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    public function duplicate(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    private function owns(User $user, Transaction $transaction): bool
    {
        return (int) $transaction->user_id === (int) $user->id;
    }
}
