<?php

namespace App\Providers;

use App\Listeners\RecordLastLogin;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::subscribe(RecordLastLogin::class);
        Transaction::observe(TransactionObserver::class);
    }
}
