@extends('tenant.layouts.app')

@section('title', $tenant->name . ' | Editar producto')

@section('header')
    Editar producto
@endsection

@section('subheader')
    Actualiza la información del producto seleccionado.
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
        <form method="POST" action="{{ route('tenant.products.update', [$tenant, $product]) }}" enctype="multipart/form-data" class="space-y-6">
            @method('PUT')
            @include('tenant.products.form', ['product' => $product])
        </form>
    </div>
@endsection
