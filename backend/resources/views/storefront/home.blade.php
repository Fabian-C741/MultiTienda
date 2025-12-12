@extends('storefront.layouts.app')

@section('title', $tenant->name . ' - Tienda Online')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-slate-900 to-slate-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold">{{ $tenant->getSetting('brand.tagline', 'Bienvenido a nuestra tienda') }}</h1>
                <p class="mt-4 text-lg text-slate-300">Descubrí nuestros productos y ofertas especiales</p>
                <a href="{{ route('storefront.catalog', $tenant) }}" class="mt-8 inline-flex items-center px-8 py-3 btn-brand text-white rounded-lg font-medium text-lg">
                    Ver catálogo
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Categorías destacadas --}}
    @if($categories->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Categorías</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($categories->take(4) as $category)
                        <a href="{{ route('storefront.category', [$tenant, $category->slug]) }}" class="group relative aspect-square rounded-xl overflow-hidden bg-slate-100">
                            @if($category->image_url)
                                <img src="{{ asset('storage/' . $category->image_url) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <h3 class="text-white font-semibold text-lg">{{ $category->name }}</h3>
                                <p class="text-white/80 text-sm">{{ $category->products_count }} productos</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Productos destacados --}}
    @if($featuredProducts->isNotEmpty())
        <section class="py-12 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Productos destacados</h2>
                    <a href="{{ route('storefront.catalog', $tenant) }}" class="text-brand hover:underline">Ver todos →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <article class="bg-white rounded-xl border border-slate-200 overflow-hidden group hover:shadow-lg transition-shadow">
                            <a href="{{ route('storefront.product', [$tenant, $product->slug]) }}" class="block">
                                <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <h3 class="font-medium text-slate-900 truncate group-hover:text-brand transition-colors">
                                    <a href="{{ route('storefront.product', [$tenant, $product->slug]) }}">{{ $product->name }}</a>
                                </h3>
                                <p class="mt-2 text-lg font-bold text-brand">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Últimos productos --}}
    @if($latestProducts->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Últimos productos</h2>
                    <a href="{{ route('storefront.catalog', [$tenant, 'sort' => 'newest']) }}" class="text-brand hover:underline">Ver todos →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($latestProducts as $product)
                        <article class="bg-white rounded-xl border border-slate-200 overflow-hidden group hover:shadow-lg transition-shadow">
                            <a href="{{ route('storefront.product', [$tenant, $product->slug]) }}" class="block">
                                <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @endif
                                    <span class="absolute top-2 left-2 bg-brand text-white text-xs px-2 py-1 rounded">Nuevo</span>
                                </div>
                            </a>
                            <div class="p-4">
                                <h3 class="font-medium text-slate-900 truncate">{{ $product->name }}</h3>
                                <p class="mt-2 text-lg font-bold text-brand">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA WhatsApp --}}
    @if($tenant->getSetting('social.whatsapp'))
        <section class="py-12 bg-emerald-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl font-bold text-white">¿Tenés alguna consulta?</h2>
                <p class="mt-2 text-emerald-100">Escribinos por WhatsApp y te respondemos al instante</p>
                <a href="https://wa.me/{{ $tenant->getSetting('social.whatsapp') }}" target="_blank" class="mt-6 inline-flex items-center px-8 py-3 bg-white text-emerald-600 rounded-lg font-medium hover:bg-emerald-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
            </div>
        </section>
    @endif
@endsection
