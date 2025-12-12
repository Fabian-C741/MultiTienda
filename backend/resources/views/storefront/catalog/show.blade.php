@extends('storefront.layouts.app')

@section('title', $product->name . ' - ' . $tenant->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('storefront.home', $tenant) }}" class="text-slate-500 hover:text-brand">Inicio</a></li>
                <li class="text-slate-400">/</li>
                <li><a href="{{ route('storefront.catalog', $tenant) }}" class="text-slate-500 hover:text-brand">Catálogo</a></li>
                @if($product->category)
                    <li class="text-slate-400">/</li>
                    <li><a href="{{ route('storefront.catalog', [$tenant, 'category' => $product->category->slug]) }}" class="text-slate-500 hover:text-brand">{{ $product->category->name }}</a></li>
                @endif
                <li class="text-slate-400">/</li>
                <li class="text-slate-900 font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-2 gap-12">
            {{-- Galería de imágenes --}}
            <div>
                <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden">
                    @if($product->image_url)
                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" id="main-image">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                @if($product->media->count() > 1)
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        @foreach($product->media as $media)
                            <button onclick="document.getElementById('main-image').src='{{ $media->url }}'" class="aspect-square bg-slate-100 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand focus:border-brand transition-colors">
                                <img src="{{ $media->url }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Información del producto --}}
            <div>
                @if($product->category)
                    <p class="text-sm text-brand font-medium mb-2">{{ $product->category->name }}</p>
                @endif
                
                <h1 class="text-3xl font-bold text-slate-900">{{ $product->name }}</h1>
                
                <p class="mt-4 text-3xl font-bold text-brand">${{ number_format($product->price, 2) }}</p>

                <div class="mt-6 flex items-center space-x-4">
                    @if($product->isInStock())
                        <span class="inline-flex items-center text-emerald-600">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            En stock ({{ $product->stock }} disponibles)
                        </span>
                    @else
                        <span class="inline-flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Sin stock
                        </span>
                    @endif
                </div>

                @if($product->description)
                    <div class="mt-8 prose prose-slate">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif

                @if($product->isInStock())
                    <form method="POST" action="{{ route('storefront.cart.add', $tenant) }}" class="mt-8">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border border-slate-300 rounded-lg">
                                <button type="button" onclick="decrementQty()" class="px-4 py-3 text-slate-600 hover:text-brand">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-16 text-center border-0 focus:ring-0">
                                <button type="button" onclick="incrementQty()" class="px-4 py-3 text-slate-600 hover:text-brand">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                            
                            <button type="submit" class="flex-1 py-3 btn-brand text-white rounded-lg font-medium text-lg">
                                Agregar al carrito
                            </button>
                        </div>
                    </form>
                @else
                    <button disabled class="mt-8 w-full py-3 bg-slate-200 text-slate-500 rounded-lg font-medium text-lg cursor-not-allowed">
                        Producto no disponible
                    </button>
                @endif

                {{-- WhatsApp --}}
                @if($tenant->getSetting('social.whatsapp'))
                    <a href="https://wa.me/{{ $tenant->getSetting('social.whatsapp') }}?text={{ urlencode('Hola! Me interesa el producto: ' . $product->name) }}" target="_blank" class="mt-4 w-full py-3 border-2 border-emerald-500 text-emerald-600 rounded-lg font-medium text-lg flex items-center justify-center hover:bg-emerald-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Consultar por WhatsApp
                    </a>
                @endif
            </div>
        </div>

        {{-- Productos relacionados --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Productos relacionados</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <article class="bg-white rounded-xl border border-slate-200 overflow-hidden group hover:shadow-lg transition-shadow">
                            <a href="{{ route('storefront.product', [$tenant, $related->slug]) }}" class="block">
                                <div class="aspect-square bg-slate-100">
                                    @if($related->image_url)
                                        <img src="{{ asset('storage/' . $related->image_url) }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-slate-900 truncate">{{ $related->name }}</h3>
                                    <p class="mt-1 font-bold text-brand">${{ number_format($related->price, 2) }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    const maxStock = {{ $product->stock }};
    
    function incrementQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) < maxStock) {
            input.value = parseInt(input.value) + 1;
        }
    }
    
    function decrementQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }
</script>
@endpush
