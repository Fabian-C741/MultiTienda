@extends('storefront.layouts.app')

@section('title', 'Pago Exitoso - ' . $tenant->settings['store_name'] ?? $tenant->name)

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-auto text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Ícono de éxito -->
            <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Pago Exitoso!</h1>
            <p class="text-gray-600 mb-6">
                Tu pago ha sido procesado correctamente.
            </p>

            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800">
                    <span class="font-semibold">Pedido:</span> {{ $order->order_number }}
                </p>
                <p class="text-lg font-bold text-green-600 mt-1">
                    Total: ${{ number_format($order->total, 2) }}
                </p>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Recibirás un email de confirmación en <strong>{{ $order->customer_email }}</strong> con los detalles de tu pedido.
            </p>

            <div class="space-y-3">
                <a href="{{ route('storefront.checkout.confirmation', [$tenant, $order->order_number]) }}" 
                   class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                    Ver Detalles del Pedido
                </a>
                <a href="{{ route('storefront.catalog', $tenant) }}" 
                   class="block w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition font-medium">
                    Seguir Comprando
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
