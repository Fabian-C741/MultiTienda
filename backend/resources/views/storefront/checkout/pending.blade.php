@extends('storefront.layouts.app')

@section('title', 'Pago Pendiente - ' . $tenant->settings['store_name'] ?? $tenant->name)

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-auto text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Ícono de pendiente -->
            <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pago Pendiente</h1>
            <p class="text-gray-600 mb-6">
                Tu pago está siendo procesado. Te notificaremos cuando se confirme.
            </p>

            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <span class="font-semibold">Pedido:</span> {{ $order->order_number }}
                </p>
                <p class="text-lg font-bold text-yellow-600 mt-1">
                    Total: ${{ number_format($order->total, 2) }}
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-semibold text-gray-900 mb-2">¿Qué significa esto?</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Tu pago está en proceso de verificación.
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        Te enviaremos un email cuando se confirme.
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zm7-2a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L12 12.414l-2.293 2.293a1 1 0 01-1.414-1.414l3-3A1 1 0 0112 10z" clip-rule="evenodd"/>
                        </svg>
                        Este proceso puede demorar unos minutos.
                    </li>
                </ul>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Recibirás una notificación en <strong>{{ $order->customer_email }}</strong>
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

            <!-- Ayuda -->
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-500">
                    ¿Tienes alguna duda? 
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
