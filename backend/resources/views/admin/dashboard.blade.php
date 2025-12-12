@extends('admin.layouts.app')

@section('title', 'Dashboard | Super Admin')

@section('header')
    Dashboard
@endsection

@section('subheader')
    Estado general de la plataforma multitienda.
@endsection

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total de tiendas</p>
            <p class="text-3xl font-semibold text-slate-800">{{ $stats['tenants_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Tiendas activas</p>
            <p class="text-3xl font-semibold text-emerald-600">{{ $stats['active_tenants'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Super administradores</p>
            <p class="text-3xl font-semibold text-indigo-600">{{ $stats['super_admins'] }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Tiendas recientes</h2>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Ver todas</a>
        </div>
        <div class="divide-y divide-slate-200">
            @forelse ($recentTenants as $tenant)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-slate-800">{{ $tenant->name }}</p>
                        <p class="text-sm text-slate-500">Slug: {{ $tenant->slug }} | Dominio: {{ $tenant->domain ?? '—' }}</p>
                    </div>
                    <span class="text-sm px-3 py-1 rounded-full {{ $tenant->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                        {{ $tenant->is_active ? 'Activa' : 'Suspendida' }}
                    </span>
                </div>
            @empty
                <p class="px-6 py-4 text-sm text-slate-500">Aún no hay tiendas registradas.</p>
            @endforelse
        </div>
    </div>
@endsection
