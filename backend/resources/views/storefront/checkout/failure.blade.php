@extends('storefront.layouts.app')

@section('title', 'Pago Fallido - ' . $tenant->settings['store_name'] ?? $tenant->name)

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-auto text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Ícono de error -->
            <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pago Fallido</h1>
            <p class="text-gray-600 mb-6">
                Hubo un problema procesando tu pago. Por favor, intenta nuevamente.
            </p>

            <div class="bg-red-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-800">
                    <span class="font-semibold">Pedido:</span> {{ $order->order_number }}
                </p>
                <p class="text-sm text-red-600 mt-1">
                    El pago no pudo ser procesado.
                </p>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Tu pedido ha sido guardado. Puedes intentar pagar nuevamente o elegir otro método de pago.
            </p>

            <div class="space-y-3">
                <a href="{{ route('storefront.checkout.index', $tenant) }}" 
                   class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                    Intentar Nuevamente
                </a>
                <a href="{{ route('storefront.catalog', $tenant) }}" 
                   class="block w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition font-medium">
                    Volver a la Tienda
                </a>
            </div>

            <!-- Ayuda -->
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-500">
                    ¿Necesitas ayuda? 
                    @if($tenant->settings['whatsapp'] ?? null)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->settings['whatsapp']) }}" 
                           class="text-green-600 hover:underline" target="_blank">
                            Contáctanos por WhatsApp
                        </a>
                    @else
                        <a href="mailto:{{ $tenant->settings['email'] ?? '' }}" class="text-blue-600 hover:underline">
                            Contáctanos
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
