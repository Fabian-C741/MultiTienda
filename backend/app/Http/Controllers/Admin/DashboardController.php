<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'tenants_count' => Tenant::query()->count(),
            'active_tenants' => Tenant::query()->where('is_active', true)->count(),
            'super_admins' => User::query()->where('role', 'super_admin')->count(),
        ];

        $recentTenants = Tenant::query()->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentTenants'));
    }
}
