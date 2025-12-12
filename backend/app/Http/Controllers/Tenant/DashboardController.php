<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(Tenant $tenant): View
    {
        $stats = [
            'products_total' => Product::query()->count(),
            'products_published' => Product::query()->active()->count(),
        ];

        return view('tenant.dashboard', compact('tenant', 'stats'));
    }
}
