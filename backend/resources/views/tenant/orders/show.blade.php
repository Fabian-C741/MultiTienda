@extends('tenant.layouts.app')

@section('title', 'Pedido ' . $order->order_number)

@section('header')
    Pedido {{ $order->order_number }}
@endsection

@section('subheader')
    Creado el {{ $order->created_at->format('d/m/Y H:i') }}
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detalles del pedido --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Productos --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Productos</h3>
                </div>
                <div class="divide-y divide-slate-200">
                    @foreach($order->items as $item)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($item->product?->image_url)
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                    <p class="text-sm text-slate-500">{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                                </div>
                            </div>
                            <p class="font-medium text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-slate-50 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="text-slate-900">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Envío</span>
                            <span class="text-slate-900">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                    @endif
                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Descuento</span>
                            <span class="text-emerald-600">-${{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-semibold pt-2 border-t border-slate-200">
                        <span>Total</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Cliente --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Cliente</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Nombre</p>
                        <p class="font-medium text-slate-900">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Email</p>
                        <p class="font-medium text-slate-900">{{ $order->customer_email }}</p>
                    </div>
                    @if($order->customer_phone)
                        <div>
                            <p class="text-sm text-slate-500">Teléfono</p>
                            <p class="font-medium text-slate-900">{{ $order->customer_phone }}</p>
                        </div>
                    @endif
                    @if($order->shipping_address)
                        <div>
                            <p class="text-sm text-slate-500">Dirección de envío</p>
                            <p class="font-medium text-slate-900">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                {{ $order->shipping_country }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estado y acciones --}}
        <div class="space-y-6">
            {{-- Estado actual --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Estado</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-emerald-100 text-emerald-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $paymentColors = [
                            'pending' => 'bg-amber-100 text-amber-800',
                            'paid' => 'bg-emerald-100 text-emerald-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-slate-100 text-slate-800',
                        ];
                    @endphp
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Estado del pedido</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-800' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Estado del pago</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-slate-100 text-slate-800' }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    @if($order->payment_method)
                        <div>
                            <p class="text-sm text-slate-500">Método de pago</p>
                            <p class="font-medium text-slate-900">{{ ucfirst($order->payment_method) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actualizar estado --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Actualizar</h3>
                </div>
                <form method="POST" action="{{ route('tenant.orders.update', $order) }}" class="px-6 py-4 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="text-sm font-medium text-slate-700">Estado del pedido</label>
                        <select name="status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
                            <option value="pending" @selected($order->status === 'pending')>Pendiente</option>
                            <option value="processing" @selected($order->status === 'processing')>Procesando</option>
                            <option value="paid" @selected($order->status === 'paid')>Pagado</option>
                            <option value="shipped" @selected($order->status === 'shipped')>Enviado</option>
                            <option value="delivered" @selected($order->status === 'delivered')>Entregado</option>
                            <option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Estado del pago</label>
                        <select name="payment_status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">
                            <option value="pending" @selected($order->payment_status === 'pending')>Pendiente</option>
                            <option value="paid" @selected($order->payment_status === 'paid')>Pagado</option>
                            <option value="failed" @selected($order->payment_status === 'failed')>Fallido</option>
                            <option value="refunded" @selected($order->payment_status === 'refunded')>Reembolsado</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Notas internas</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2">{{ $order->notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Guardar cambios
                    </button>
                </form>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Historial</h3>
                </div>
                <div class="px-6 py-4">
                    <ol class="relative border-l border-slate-200 space-y-4">
                        <li class="ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5"></div>
                            <p class="text-sm font-medium text-slate-900">Pedido creado</p>
                            <p class="text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </li>
                        @if($order->paid_at)
                            <li class="ml-4">
                                <div class="absolute w-3 h-3 bg-emerald-600 rounded-full -left-1.5"></div>
                                <p class="text-sm font-medium text-slate-900">Pago recibido</p>
                                <p class="text-xs text-slate-500">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
                            </li>
                        @endif
                        @if($order->shipped_at)
                            <li class="ml-4">
                                <div class="absolute w-3 h-3 bg-purple-600 rounded-full -left-1.5"></div>
                                <p class="text-sm font-medium text-slate-900">Enviado</p>
                                <p class="text-xs text-slate-500">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                            </li>
                        @endif
                        @if($order->delivered_at)
                            <li class="ml-4">
                                <div class="absolute w-3 h-3 bg-green-600 rounded-full -left-1.5"></div>
                                <p class="text-sm font-medium text-slate-900">Entregado</p>
                                <p class="text-xs text-slate-500">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                            </li>
                        @endif
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection
