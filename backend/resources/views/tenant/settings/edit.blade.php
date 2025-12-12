@extends('tenant.layouts.app')

@section('title', $tenant->name . ' | Apariencia')

@section('header')
    Apariencia y marca
@endsection

@section('subheader')
    Personaliza los colores, logotipo y pie de página de tu tienda.
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

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-8">
        <form method="POST" action="{{ route('tenant.settings.update', $tenant) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <section class="space-y-4">
                <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Identidad</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <label for="brand_name" class="text-sm font-medium text-slate-700">Nombre público</label>
                        <input id="brand_name" name="brand[name]" type="text" value="{{ old('brand.name', data_get($settings, 'brand.name', $tenant->name)) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="space-y-1">
                        <label for="brand_tagline" class="text-sm font-medium text-slate-700">Eslogan</label>
                        <input id="brand_tagline" name="brand[tagline]" type="text" value="{{ old('brand.tagline', data_get($settings, 'brand.tagline')) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Colores</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <label for="primary_color" class="text-sm font-medium text-slate-700">Color primario</label>
                        <input id="primary_color" name="theme[primary_color]" type="text" value="{{ old('theme.primary_color', data_get($settings, 'theme.primary_color', '#6366F1')) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="#6366F1">
                    </div>
                    <div class="space-y-1">
                        <label for="secondary_color" class="text-sm font-medium text-slate-700">Color secundario</label>
                        <input id="secondary_color" name="theme[secondary_color]" type="text" value="{{ old('theme.secondary_color', data_get($settings, 'theme.secondary_color', '#F97316')) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="#F97316">
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Imágenes</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label for="logo" class="text-sm font-medium text-slate-700">Logotipo</label>
                        <input id="logo" name="appearance[logo]" type="file" accept="image/*" class="w-full text-sm text-slate-600">
                        @if ($logo = data_get($settings, 'appearance.logo'))
                            <img src="{{ $logo }}" alt="Logotipo actual" class="mt-2 h-16 object-contain">
                        @endif
                    </div>
                    <div class="space-y-2">
                        <label for="favicon" class="text-sm font-medium text-slate-700">Favicon</label>
                        <input id="favicon" name="appearance[favicon]" type="file" accept="image/*" class="w-full text-sm text-slate-600">
                        @if ($favicon = data_get($settings, 'appearance.favicon'))
                            <img src="{{ $favicon }}" alt="Favicon actual" class="mt-2 h-12 w-12 object-contain">
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Pie de página</h2>
                <div class="space-y-1">
                    <label for="footer_text" class="text-sm font-medium text-slate-700">Mensaje de copyright</label>
                    <input id="footer_text" name="footer[text]" type="text" value="{{ old('footer.text', data_get($settings, 'footer.text')) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </section>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('tenant.dashboard', $tenant) }}" class="px-4 py-2 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar cambios</button>
            </div>
        </form>
    </div>
@endsection
