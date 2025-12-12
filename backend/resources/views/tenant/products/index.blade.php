@extends('tenant.layouts.app')

@section('title', $tenant->name . ' | Productos')

@section('header')
    Productos
@endsection

@section('subheader')
    Administra el catálogo de tu tienda.
@endsection

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('tenant.products.create', $tenant) }}" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Nuevo producto</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3">Precio</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">
                            <div>{{ $product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $product->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $product->status === 'published' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                {{ $product->status === 'published' ? 'Publicado' : 'Borrador' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('tenant.products.edit', [$tenant, $product]) }}" class="text-indigo-600 hover:text-indigo-700">Editar</a>
                            <form action="{{ route('tenant.products.destroy', [$tenant, $product]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('¿Quieres eliminar este producto?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-slate-500">Aún no hay productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
