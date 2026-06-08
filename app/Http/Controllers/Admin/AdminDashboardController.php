<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $adminUsers = User::where('role', User::ROLE_ADMIN)->count();
        $regularUsers = User::where('role', User::ROLE_USER)->count();
        $totalCategories = Category::count();
        $totalTransactions = Transaction::count();
        $newUsersLast30 = User::where('created_at', '>=', now()->subDays(30))->count();
        $newTxLast30 = Transaction::where('created_at', '>=', now()->subDays(30))->count();
        $activeLast7 = User::where('last_login_at', '>=', now()->subDays(7))->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'adminUsers', 'regularUsers',
            'totalCategories', 'totalTransactions', 'newUsersLast30', 'newTxLast30', 'activeLast7'
        ));
    }
}
