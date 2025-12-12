@extends('admin.layouts.app')

@section('title', 'Tiendas | Super Admin')

@section('header')
    Tiendas
@endsection

@section('subheader')
    Gestiona las tiendas creadas para tus clientes.
@endsection

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Nueva tienda</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Slug</th>
                    <th class="px-6 py-3">Dominio</th>
                    <th class="px-6 py-3">Base de datos</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $tenant->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $tenant->slug }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $tenant->domain ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $tenant->database }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium {{ $tenant->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                {{ $tenant->is_active ? 'Activa' : 'Suspendida' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-700">Editar</a>
                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('¿Deseas eliminar esta tienda? Esta acción no elimina su base de datos automáticamente.');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-slate-500">Aún no existen tiendas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $tenants->links() }}
        </div>
    </div>
@endsection
