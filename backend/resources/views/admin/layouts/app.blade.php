<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Panel Super Admin')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div class="min-h-screen flex">
            <aside class="hidden lg:flex lg:w-64 bg-white shadow-sm border-r border-slate-200">
                <div class="w-full p-6 space-y-6">
                    <span class="text-lg font-semibold">Tienda Multiplataforma</span>
                    <nav class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md @if(request()->routeIs('admin.dashboard')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">Dashboard</a>
                        <a href="{{ route('admin.tenants.index') }}" class="block px-3 py-2 rounded-md @if(request()->routeIs('admin.tenants.*')) bg-indigo-50 text-indigo-600 @else hover:bg-slate-100 @endif">Tiendas</a>
                    </nav>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-left rounded-md bg-red-50 text-red-600 hover:bg-red-100">Cerrar sesión</button>
                    </form>
                </div>
            </aside>
            <div class="flex-1 flex flex-col">
                <header class="bg-white border-b border-slate-200 shadow-sm">
                    <div class="max-w-5xl mx-auto w-full px-4 py-4 flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-800">@yield('header')</h1>
                            @hasSection('subheader')
                                <p class="text-sm text-slate-500">@yield('subheader')</p>
                            @endif
                        </div>
                        <div class="lg:hidden">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-md bg-red-50 text-red-600">Salir</button>
                            </form>
                        </div>
                    </div>
                </header>
                <main class="flex-1">
                    <div class="max-w-5xl mx-auto w-full px-4 py-6">
                        @if (session('status'))
                            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3">
                                {{ session('status') }}
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
