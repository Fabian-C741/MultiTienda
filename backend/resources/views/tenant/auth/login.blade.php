<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acceder | {{ $tenant->name }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-slate-900">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="w-full max-w-md bg-white border border-slate-200 shadow-sm rounded-xl p-8 space-y-6">
                <div class="space-y-1 text-center">
                    <h1 class="text-2xl font-semibold">Administrar {{ $tenant->name }}</h1>
                    <p class="text-sm text-slate-500">Ingresa con tu cuenta de administrador.</p>
                </div>
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 border border-red-200 text-red-600 px-4 py-3">
                        <ul class="list-disc pl-5 space-y-1 text-left">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('tenant.login.perform', $tenant) }}" class="space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-slate-700">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <div class="space-y-1">
                        <label for="password" class="text-sm font-medium text-slate-700">Contraseña</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <label class="inline-flex items-center space-x-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        <span>Mantener sesión activa</span>
                    </label>
                    <button type="submit" class="w-full bg-indigo-600 text-white rounded-md px-3 py-2 font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Acceder</button>
                </form>
            </div>
        </div>
    </body>
</html>
