<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        $statuses = [
            Order::STATUS_PENDING => 'Pendiente',
            Order::STATUS_PROCESSING => 'Procesando',
            Order::STATUS_PAID => 'Pagado',
            Order::STATUS_SHIPPED => 'Enviado',
            Order::STATUS_DELIVERED => 'Entregado',
            Order::STATUS_CANCELLED => 'Cancelado',
        ];

        return view('tenant.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product');

        return view('tenant.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,paid,shipped,delivered,cancelled'],
            'payment_status' => ['sometimes', 'string', 'in:pending,paid,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($validated);

        // Actualizar timestamps según estado
        if ($validated['status'] === Order::STATUS_SHIPPED && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        if ($validated['status'] === Order::STATUS_DELIVERED && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }

        if (($validated['payment_status'] ?? null) === Order::PAYMENT_PAID && !$order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return Redirect::back()->with('status', 'Pedido actualizado correctamente.');
    }
}
