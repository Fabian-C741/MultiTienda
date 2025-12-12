<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $tenant->name)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @php
        $brandColor = $tenant->getSetting('brand.primary_color', '#4f46e5');
        $brandName = $tenant->getSetting('brand.name', $tenant->name);
        $logo = $tenant->getSetting('brand.logo_url');
    @endphp
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '{{ $brandColor }}'
                    }
                }
            }
        }
    </script>
    <style>
        .btn-brand { background-color: {{ $brandColor }}; }
        .btn-brand:hover { filter: brightness(0.9); }
        .text-brand { color: {{ $brandColor }}; }
        .border-brand { border-color: {{ $brandColor }}; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('storefront.home', $tenant) }}" class="flex items-center space-x-3">
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $brandName }}" class="h-10">
                    @else
                        <span class="text-xl font-bold text-brand">{{ $brandName }}</span>
                    @endif
                </a>

                {{-- Navegación --}}
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('storefront.home', $tenant) }}" class="text-slate-600 hover:text-brand">Inicio</a>
                    <a href="{{ route('storefront.catalog', $tenant) }}" class="text-slate-600 hover:text-brand">Catálogo</a>
                </nav>

                {{-- Carrito --}}
                <div class="flex items-center space-x-4">
                    <a href="{{ route('storefront.cart.index', $tenant) }}" class="relative p-2 text-slate-600 hover:text-brand">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-brand text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            0
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Contenido principal --}}
    <main class="flex-1">
        @if(session('status'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-slate-500 text-sm">
                    © {{ date('Y') }} {{ $brandName }}. Todos los derechos reservados.
                </p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    @if($tenant->getSetting('social.whatsapp'))
                        <a href="https://wa.me/{{ $tenant->getSetting('social.whatsapp') }}" target="_blank" class="text-slate-400 hover:text-emerald-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                    @endif
                    @if($tenant->getSetting('social.instagram'))
                        <a href="https://instagram.com/{{ $tenant->getSetting('social.instagram') }}" target="_blank" class="text-slate-400 hover:text-pink-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
