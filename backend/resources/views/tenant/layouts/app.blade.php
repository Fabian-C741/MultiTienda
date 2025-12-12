<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', $tenant->name . ' | Panel de Tienda')</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-slate-900">
        <div class="min-h-screen flex">
            <aside class="hidden md:flex md:w-64 bg-white border-r border-slate-200">
                <div class="w-full p-6 space-y-6">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Tienda</p>
                        <p class="text-lg font-semibold text-slate-800">{{ $tenant->name }}</p>
                        <p class="text-xs text-slate-500">{{ $tenant->domain ?? $tenant->slug }}</p>
                    </div>
                    <nav class="space-y-1 text-sm">
                        <a href="{{ route('tenant.dashboard', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.dashboard')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            Resumen
                        </a>
                        <a href="{{ route('tenant.products.index', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.products.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            Productos
                        </a>
                        <a href="{{ route('tenant.categories.index', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.categories.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            Categorías
                        </a>
                        <a href="{{ route('tenant.orders.index', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.orders.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            Pedidos
                        </a>
                        <a href="{{ route('tenant.payments.index', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.payments.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Pagos
                        </a>
                        <a href="{{ route('tenant.media.index', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.media.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Galería
                        </a>
                        <a href="{{ route('tenant.settings.edit', $tenant) }}" class="flex items-center px-3 py-2 rounded-md @if(request()->routeIs('tenant.settings.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Apariencia
                        </a>
                    </nav>
                    <div class="pt-4 border-t border-slate-200">
                        <a href="{{ route('storefront.home', $tenant) }}" target="_blank" class="flex items-center px-3 py-2 rounded-md text-slate-600 hover:bg-slate-100 text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Ver tienda
                        </a>
                    </div>
                    <form method="POST" action="{{ route('tenant.logout', $tenant) }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 rounded-md bg-red-50 text-red-600 hover:bg-red-100 text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </aside>
            <div class="flex-1 flex flex-col">
                <header class="bg-white border-b border-slate-200 shadow-sm">
                    <div class="max-w-5xl mx-auto w-full px-4 py-4 flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-800">@yield('header')</h1>
                            @hasSection('subheader')
                                <p class="text-xs text-slate-500">@yield('subheader')</p>
                            @endif
                        </div>
                        <div class="md:hidden">
                            <form method="POST" action="{{ route('tenant.logout', $tenant) }}">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-md bg-red-50 text-red-600">Salir</button>
                            </form>
                        </div>
                    </div>
                </header>
                <main class="flex-1">
                    <div class="max-w-5xl mx-auto w-full px-4 py-6">
                        @if (session('status'))
                            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                                {{ session('error') }}
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
