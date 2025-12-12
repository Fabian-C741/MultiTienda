@extends('storefront.layouts.app')

@section('title', 'Catálogo - ' . $tenant->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar de categorías --}}
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl border border-slate-200 p-4 sticky top-24">
                    <h3 class="font-semibold text-slate-900 mb-4">Categorías</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('storefront.catalog', $tenant) }}" class="block px-3 py-2 rounded-lg {{ !request('category') ? 'bg-slate-100 text-brand font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Todos los productos
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('storefront.catalog', [$tenant, 'category' => $category->slug]) }}" class="block px-3 py-2 rounded-lg {{ request('category') === $category->slug ? 'bg-slate-100 text-brand font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            {{-- Lista de productos --}}
            <div class="flex-1">
                {{-- Barra de búsqueda y filtros --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar productos..." class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand/50">
                        </div>
                        <select name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-4 py-2">
                            <option value="newest" @selected(request('sort') === 'newest')>Más recientes</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Menor precio</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Mayor precio</option>
                            <option value="name" @selected(request('sort') === 'name')>Nombre A-Z</option>
                        </select>
                        <button type="submit" class="px-6 py-2 btn-brand text-white rounded-lg">Buscar</button>
                    </form>
                </div>

                @if($products->isEmpty())
                    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-slate-900">No encontramos productos</h3>
                        <p class="mt-2 text-slate-500">Intenta con otra búsqueda o categoría.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <article class="bg-white rounded-xl border border-slate-200 overflow-hidden group hover:shadow-lg transition-shadow">
                                <a href="{{ route('storefront.product', [$tenant, $product->slug]) }}" class="block">
                                    <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                        @if($product->image_url)
                                            <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if(!$product->isInStock())
                                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">Sin stock</span>
                                        @endif
                                    </div>
                                </a>
                                <div class="p-4">
                                    @if($product->category)
                                        <p class="text-xs text-slate-500 mb-1">{{ $product->category->name }}</p>
                                    @endif
                                    <h3 class="font-medium text-slate-900 group-hover:text-brand transition-colors">
                                        <a href="{{ route('storefront.product', [$tenant, $product->slug]) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="mt-2 text-lg font-bold text-brand">${{ number_format($product->price, 2) }}</p>
                                    
                                    @if($product->isInStock())
                                        <form method="POST" action="{{ route('storefront.cart.add', $tenant) }}" class="mt-4">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="w-full py-2 btn-brand text-white rounded-lg text-sm font-medium">
                                                Agregar al carrito
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="mt-4 w-full py-2 bg-slate-200 text-slate-500 rounded-lg text-sm font-medium cursor-not-allowed">
                                            Sin stock
                                        </button>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
