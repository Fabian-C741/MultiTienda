@extends('admin.layouts.app')

@section('title', 'Nueva tienda | Super Admin')

@section('header')
    Crear nueva tienda
@endsection

@section('subheader')
    Define los datos base de la tienda y su conexión dedicada.
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

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-6">
            @include('admin.tenants.form', ['tenant' => null])
        </form>
    </div>
@endsection
