@extends('tenant.layouts.app')

@section('title', $tenant->name . ' | Dashboard')

@section('header')
    Resumen general
@endsection

@section('subheader')
    Revisa métricas rápidas de tu tienda.
@endsection

@section('content')
    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Productos totales</p>
            <p class="text-3xl font-semibold text-slate-800">{{ $stats['products_total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Productos publicados</p>
            <p class="text-3xl font-semibold text-emerald-600">{{ $stats['products_published'] }}</p>
        </div>
    </div>
@endsection
