@php($product = $product ?? null)
@php($categories = \App\Models\Tenant\Category::active()->ordered()->get())

@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="name">Nombre *</label>
        <input id="name" name="name" type="text" value="{{ old('name', optional($product)->name) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="slug">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', optional($product)->slug) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="producto-ejemplo">
        <p class="text-xs text-slate-500">Si lo dejas vacío se generará automáticamente.</p>
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="sku">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', optional($product)->sku) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="PROD-001">
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="category_id">Categoría</label>
        <select id="category_id" name="category_id" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Sin categoría</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', optional($product)->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2 space-y-1">
        <label class="text-sm font-medium text-slate-700" for="description">Descripción</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', optional($product)->description) }}</textarea>
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="price">Precio *</label>
        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', optional($product)->price ?? 0) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="compare_price">Precio anterior (tachado)</label>
        <input id="compare_price" name="compare_price" type="number" step="0.01" min="0" value="{{ old('compare_price', optional($product)->compare_price) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <p class="text-xs text-slate-500">Opcional. Muestra como precio anterior.</p>
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="stock">Stock</label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', optional($product)->stock ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="space-y-1">
        <label class="text-sm font-medium text-slate-700" for="status">Estado</label>
        <select id="status" name="status" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="draft" @selected(old('status', optional($product)->status ?? 'draft') === 'draft')>Borrador</option>
            <option value="published" @selected(old('status', optional($product)->status ?? 'draft') === 'published')>Publicado</option>
            <option value="archived" @selected(old('status', optional($product)->status) === 'archived')>Archivado</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', optional($product)->is_featured)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm font-medium text-slate-700">Producto destacado</span>
        </label>
        <p class="text-xs text-slate-500 mt-1">Se mostrará en la página principal de la tienda.</p>
    </div>
    <div class="space-y-2 md:col-span-2">
        <label class="text-sm font-medium text-slate-700" for="image">Imagen principal</label>
        <input id="image" name="image" type="file" accept="image/*" class="w-full text-sm text-slate-600">
        @if (optional($product)->image_url)
            <div class="flex items-center space-x-3 mt-2">
                <img src="{{ asset('storage/' . $product->image_url) }}" alt="Imagen actual" class="h-16 w-16 object-cover rounded-md">
                <label class="inline-flex items-center space-x-2 text-sm text-slate-600">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300">
                    <span>Eliminar imagen actual</span>
                </label>
            </div>
        @endif
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('tenant.products.index', $tenant) }}" class="px-4 py-2 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-50">Cancelar</a>
    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
</div>
