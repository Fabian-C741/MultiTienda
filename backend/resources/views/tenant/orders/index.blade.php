@extends('tenant.layouts.app')

@section('title', 'Pedidos')

@section('header')
    Pedidos
@endsection

@section('subheader')
    Gestiona los pedidos de tu tienda.
@endsection

@section('content')
    {{-- Filtros --}}
    <div class="mb-6 bg-white rounded-xl border border-slate-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Número, nombre o email..." class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select name="status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-1">Pago</label>
                <select name="payment_status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>Pendiente</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>Pagado</option>
                    <option value="failed" @selected(request('payment_status') === 'failed')>Fallido</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filtrar</button>
            <a href="{{ route('tenant.orders.index') }}" class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-50">Limpiar</a>
        </form>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">No hay pedidos</h3>
            <p class="mt-2 text-slate-500">Los pedidos aparecerán aquí cuando los clientes compren.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-slate-900">{{ $order->order_number }}</span>
                                <p class="text-xs text-slate-500">{{ $order->items->count() }} productos</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-900">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-500">{{ $order->customer_email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                ${{ number_format($order->total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $paymentColors = [
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-slate-100 text-slate-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tenant.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">Ver detalles</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
@endsection
