@extends('storefront.layouts.app')

@section('title', 'Pedido confirmado - ' . $tenant->name)

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="mt-6 text-2xl font-bold text-slate-900">¡Gracias por tu pedido!</h1>
            <p class="mt-2 text-slate-600">Tu pedido ha sido recibido correctamente.</p>
            
            <div class="mt-6 p-4 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Número de pedido</p>
                <p class="text-xl font-mono font-bold text-brand">{{ $order->order_number }}</p>
            </div>

            @if($paymentInstructions)
                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-left">
                    <h3 class="font-semibold text-amber-800 mb-2">Instrucciones de pago</h3>
                    <pre class="text-sm text-amber-700 whitespace-pre-wrap">{{ $paymentInstructions }}</pre>
                </div>
            @endif

            <div class="mt-8 text-left">
                <h3 class="font-semibold text-slate-900 mb-4">Resumen del pedido</h3>
                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <div class="py-3 flex justify-between">
                            <div>
                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                <p class="text-sm text-slate-500">{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="font-medium text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-brand">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-left">
                <h3 class="font-semibold text-slate-900 mb-2">Datos de contacto</h3>
                <p class="text-slate-600">{{ $order->customer_name }}</p>
                <p class="text-slate-600">{{ $order->customer_email }}</p>
                @if($order->customer_phone)
                    <p class="text-slate-600">{{ $order->customer_phone }}</p>
                @endif
            </div>

            @if($order->shipping_address)
                <div class="mt-6 text-left">
                    <h3 class="font-semibold text-slate-900 mb-2">Dirección de envío</h3>
                    <p class="text-slate-600">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}
                    </p>
                </div>
            @endif

            <div class="mt-8">
                <a href="{{ route('storefront.catalog', $tenant) }}" class="inline-flex items-center px-6 py-3 btn-brand text-white rounded-lg font-medium">
                    Seguir comprando
                </a>
            </div>

            @if($tenant->getSetting('social.whatsapp'))
                <p class="mt-6 text-sm text-slate-500">
                    ¿Tienes dudas? 
                    <a href="https://wa.me/{{ $tenant->getSetting('social.whatsapp') }}?text={{ urlencode('Hola! Acabo de realizar el pedido ' . $order->order_number) }}" target="_blank" class="text-emerald-600 hover:underline">
                        Escríbenos por WhatsApp
                    </a>
                </p>
            @endif
        </div>
    </div>
@endsection
