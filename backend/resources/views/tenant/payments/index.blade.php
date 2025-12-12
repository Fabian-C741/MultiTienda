@extends('tenant.layouts.app')

@section('title', 'Métodos de Pago')

@section('header')
    Métodos de Pago
@endsection

@section('subheader')
    Configura las pasarelas de pago disponibles para tu tienda.
@endsection

@section('content')
    <div class="grid gap-6 md:grid-cols-2">
        @foreach($availableGateways as $gateway => $info)
            @php
                $configured = $configuredGateways[$gateway] ?? null;
                $isActive = $configured?->is_active ?? false;
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                            @if($gateway === 'mercadopago')
                                <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            @elseif($gateway === 'uala')
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            @elseif($gateway === 'transfer')
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-slate-900">{{ $info['name'] }}</h3>
                            <p class="text-sm text-slate-500">{{ $info['description'] }}</p>
                        </div>
                    </div>
                    @if($isActive)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Activo</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Inactivo</span>
                    @endif
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('tenant.payments.edit', $gateway) }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Configurar
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
