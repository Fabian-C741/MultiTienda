@php($category = $category ?? null)

<div class="grid gap-6 md:grid-cols-2">
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="name">Nombre *</label>
        <input id="name" name="name" type="text" value="{{ old('name', $category?->name) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="slug">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $category?->slug) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <p class="text-xs text-slate-500">Se genera automáticamente si lo dejas vacío.</p>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="parent_id">Categoría padre</label>
        <select id="parent_id" name="parent_id" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Sin categoría padre (raíz)</option>
            @foreach($parentCategories as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="sort_order">Orden</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="space-y-2 md:col-span-2">
        <label class="text-sm font-medium text-slate-700" for="description">Descripción</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $category?->description) }}</textarea>
    </div>

    <div class="space-y-2 md:col-span-2">
        <label class="text-sm font-medium text-slate-700" for="image">Imagen</label>
        @if($category?->image_url)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $category->image_url) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-lg">
            </div>
        @endif
        <input id="image" name="image" type="file" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="text-xs text-slate-500">JPG, PNG o GIF. Máximo 2MB.</p>
    </div>

    <div class="flex items-center space-x-2">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $category?->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <label class="text-sm font-medium text-slate-700" for="is_active">Categoría activa</label>
    </div>
</div>

<div class="mt-8 flex justify-end space-x-3">
    <a href="{{ route('tenant.categories.index') }}" class="px-4 py-2 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-50">Cancelar</a>
    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
</div>
