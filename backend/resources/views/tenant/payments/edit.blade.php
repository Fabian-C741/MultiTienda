@extends('tenant.layouts.app')

@section('title', 'Configurar ' . $gatewayInfo['name'])

@section('header')
    Configurar {{ $gatewayInfo['name'] }}
@endsection

@section('subheader')
    {{ $gatewayInfo['description'] }}
@endsection

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-600 px-4 py-3">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.payments.update', $gateway) }}" class="space-y-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="display_name">Nombre a mostrar *</label>
                    <input id="display_name" name="display_name" type="text" value="{{ old('display_name', $settings->display_name ?? $gatewayInfo['name']) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="sort_order">Orden</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $settings->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-slate-700" for="description">Descripción</label>
                    <textarea id="description" name="description" rows="2" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $settings->description) }}</textarea>
                    <p class="text-xs text-slate-500">Se mostrará al cliente al elegir método de pago.</p>
                </div>
            </div>

            @if(count($gatewayInfo['fields']) > 0)
                <div class="pt-6 border-t border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Credenciales</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach($gatewayInfo['fields'] as $field)
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700" for="credentials_{{ $field }}">
                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                </label>
                                <input 
                                    id="credentials_{{ $field }}" 
                                    name="credentials[{{ $field }}]" 
                                    type="{{ in_array($field, ['password', 'secret', 'api_key', 'access_token']) ? 'password' : 'text' }}" 
                                    value="{{ old("credentials.{$field}", $settings->credentials[$field] ?? '') }}" 
                                    class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    autocomplete="off"
                                >
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-6 border-t border-slate-200">
                <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Opciones</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $settings->is_active ?? false)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label class="text-sm font-medium text-slate-700" for="is_active">Método de pago activo</label>
                    </div>

                    @if(in_array($gateway, ['mercadopago', 'uala']))
                        <div class="flex items-center space-x-3">
                            <input id="is_sandbox" name="is_sandbox" type="checkbox" value="1" @checked(old('is_sandbox', $settings->is_sandbox ?? true)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <label class="text-sm font-medium text-slate-700" for="is_sandbox">Modo de pruebas (Sandbox)</label>
                        </div>
                        <p class="text-xs text-slate-500 ml-6">Activa este modo para hacer pruebas sin procesar pagos reales.</p>
                    @endif
                </div>
            </div>

            <div class="pt-6 flex justify-end space-x-3">
                <a href="{{ route('tenant.payments.index') }}" class="px-4 py-2 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar configuración</button>
            </div>
        </form>
    </div>

    @if($gateway === 'mercadopago')
        <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">¿Cómo obtener las credenciales de Mercado Pago?</h4>
            <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                <li>Ingresa a <a href="https://www.mercadopago.com.ar/developers" target="_blank" class="underline">mercadopago.com.ar/developers</a></li>
                <li>Crea una aplicación o selecciona una existente</li>
                <li>Ve a "Credenciales" en el menú lateral</li>
                <li>Copia el "Access Token" y "Public Key"</li>
            </ol>
        </div>
    @elseif($gateway === 'uala')
        <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">¿Cómo obtener las credenciales de Ualá Bis?</h4>
            <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                <li>Ingresa a tu cuenta de Ualá Bis</li>
                <li>Ve a "Integraciones" o "API"</li>
                <li>Genera las credenciales de API</li>
                <li>Copia el "API Key" y "Username"</li>
            </ol>
        </div>
    @endif
@endsection
