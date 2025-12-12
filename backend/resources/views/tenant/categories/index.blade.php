@extends('tenant.layouts.app')

@section('title', 'Categorías')

@section('header')
    Categorías
@endsection

@section('subheader')
    Organiza tus productos en categorías.
@endsection

@section('actions')
    <a href="{{ route('tenant.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva categoría
    </a>
@endsection

@section('content')
    @if($categories->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">No hay categorías</h3>
            <p class="mt-2 text-slate-500">Empieza creando tu primera categoría de productos.</p>
            <a href="{{ route('tenant.categories.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Crear categoría
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($categories as $category)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($category->image_url)
                                        <img src="{{ asset('storage/' . $category->image_url) }}" alt="{{ $category->name }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $category->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $category->products_count ?? $category->products()->count() }}
                            </td>
                            <td class="px-6 py-4">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Activa</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Inactiva</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('tenant.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-800">Editar</a>
                                <form method="POST" action="{{ route('tenant.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        
                        {{-- Subcategorías --}}
                        @foreach($category->children as $child)
                            <tr class="hover:bg-slate-50 bg-slate-25">
                                <td class="px-6 py-4 pl-12">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-slate-300">└</span>
                                        @if($child->image_url)
                                            <img src="{{ asset('storage/' . $child->image_url) }}" alt="{{ $child->name }}" class="w-8 h-8 rounded object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-slate-700">{{ $child->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $child->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $child->products()->count() }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($child->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Activa</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('tenant.categories.edit', $child) }}" class="text-indigo-600 hover:text-indigo-800">Editar</a>
                                    <form method="POST" action="{{ route('tenant.categories.destroy', $child) }}" class="inline" onsubmit="return confirm('¿Eliminar esta subcategoría?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
@endsection
