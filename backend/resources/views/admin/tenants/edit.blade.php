@extends('admin.layouts.app')

@section('title', 'Editar tienda | Super Admin')

@section('header')
    Editar tienda
@endsection

@section('subheader')
    Actualiza los datos de la tienda seleccionada.
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

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-600 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-6">
            @method('PUT')
            @include('admin.tenants.form', ['tenant' => $tenant])
        </form>
    </div>

    {{-- Acciones de mantenimiento --}}
    <div class="mt-8 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-4">Mantenimiento</h2>
        
        <div class="flex flex-wrap gap-4">
            {{-- Ejecutar migraciones --}}
            <form method="POST" action="{{ route('admin.tenants.migrate', $tenant) }}" onsubmit="return confirm('¿Ejecutar migraciones para esta tienda?')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Ejecutar migraciones
                </button>
            </form>

            {{-- Acceder al panel --}}
            <a href="{{ route('tenant.login.show', $tenant) }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Acceder al panel
            </a>
        </div>
    </div>

    {{-- Zona de peligro --}}
    <div class="mt-8 bg-red-50 rounded-xl border border-red-200 p-6">
        <h2 class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-4">Zona de peligro</h2>
        
        <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta tienda? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            
            <div class="flex items-center space-x-4">
                <label class="flex items-center space-x-2 text-sm text-slate-700">
                    <input type="checkbox" name="drop_database" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                    <span>Eliminar también la base de datos física</span>
                </label>
            </div>
            
            <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Eliminar tienda
            </button>
        </form>
    </div>
@endsection
