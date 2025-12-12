@extends('storefront.layouts.app')

@section('title', 'Carrito - ' . $tenant->name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-8">Tu carrito</h1>

        @if($cart->items->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-slate-900">Tu carrito está vacío</h3>
                <p class="mt-2 text-slate-500">Agrega productos para continuar comprando.</p>
                <a href="{{ route('storefront.catalog', $tenant) }}" class="mt-6 inline-flex items-center px-6 py-3 btn-brand text-white rounded-lg font-medium">
                    Ver catálogo
                </a>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Lista de items --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-200">
                        @foreach($cart->items as $item)
                            <div class="p-4 flex items-center space-x-4">
                                {{-- Imagen --}}
                                <div class="w-20 h-20 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->product?->image_url)
                                        <img src="{{ asset('storage/' . $item->product->image_url) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-slate-900 truncate">
                                        <a href="{{ route('storefront.product', [$tenant, $item->product?->slug]) }}" class="hover:text-brand">
                                            {{ $item->product?->name ?? 'Producto no disponible' }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-slate-500">${{ number_format($item->unit_price, 2) }} c/u</p>
                                </div>

                                {{-- Cantidad --}}
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('storefront.cart.update', $tenant) }}" class="flex items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}" class="p-1 text-slate-400 hover:text-brand">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="p-1 text-slate-400 hover:text-brand">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right">
                                    <p class="font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                                    <form method="POST" action="{{ route('storefront.cart.remove', $tenant) }}" class="mt-1">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-between">
                        <a href="{{ route('storefront.catalog', $tenant) }}" class="text-brand hover:underline">← Seguir comprando</a>
                        <form method="POST" action="{{ route('storefront.cart.clear', $tenant) }}">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-red-500">Vaciar carrito</button>
                        </form>
                    </div>
                </div>

                {{-- Resumen --}}
                <div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Resumen</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal ({{ $cart->item_count }} productos)</span>
                                <span>${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Envío</span>
                                <span class="text-sm">A calcular</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-brand">${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('storefront.checkout.index', $tenant) }}" class="mt-6 block w-full py-3 btn-brand text-white rounded-lg font-medium text-center">
                            Finalizar compra
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
