@extends('storefront.layouts.app')

@section('title', 'Checkout - ' . $tenant->name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-8">Finalizar compra</h1>

        <form method="POST" action="{{ route('storefront.checkout.process', $tenant) }}">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Formulario --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Datos de contacto --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Datos de contacto</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre completo *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                                @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono / WhatsApp</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                            </div>
                        </div>
                    </div>

                    {{-- Dirección de envío --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Dirección de envío</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
                                <input type="text" name="address" value="{{ old('address') }}" placeholder="Calle y número" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Ciudad</label>
                                <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Provincia</label>
                                <input type="text" name="state" value="{{ old('state') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Código postal</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                            </div>
                        </div>
                    </div>

                    {{-- Método de pago --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Método de pago</h2>
                        @error('payment_method') <p class="mb-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <label class="flex items-start p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                                    <input type="radio" name="payment_method" value="{{ $method->gateway }}" class="mt-1 text-brand focus:ring-brand" {{ old('payment_method') === $method->gateway ? 'checked' : '' }} required>
                                    <div class="ml-3">
                                        <p class="font-medium text-slate-900">{{ $method->display_name }}</p>
                                        @if($method->description)
                                            <p class="text-sm text-slate-500">{{ $method->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Notas adicionales</h2>
                        <textarea name="notes" rows="3" placeholder="¿Alguna instrucción especial para tu pedido?" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Resumen del pedido --}}
                <div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Tu pedido</h3>
                        
                        <div class="divide-y divide-slate-100">
                            @foreach($cart->items as $item)
                                <div class="py-3 flex justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900">{{ $item->product?->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Subtotal</span>
                                <span>${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Envío</span>
                                <span>A coordinar</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-brand">${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                        </div>

                        <button type="submit" class="mt-6 w-full py-3 btn-brand text-white rounded-lg font-medium">
                            Confirmar pedido
                        </button>

                        <a href="{{ route('storefront.cart.index', $tenant) }}" class="mt-3 block text-center text-sm text-slate-500 hover:text-brand">
                            ← Volver al carrito
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
