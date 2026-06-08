<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/{transaction}/duplicate', [TransactionController::class, 'duplicate'])->name('transactions.duplicate');
    Route::get('transactions/suggest-category', [TransactionController::class, 'suggestCategory'])->name('transactions.suggest-category');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/exports/transactions.csv', [ExportController::class, 'transactions'])->name('exports.transactions');
});

/*
|--------------------------------------------------------------------------
| Admin area
|--------------------------------------------------------------------------
| Privacy-first: el admin NUNCA accede a datos financieros de los usuarios.
| Solo gestiona cuentas y consulta métricas agregadas de plataforma.
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.alt');
        Route::resource('users', AdminUserController::class)->except(['show']);
    });

require __DIR__.'/auth.php';
