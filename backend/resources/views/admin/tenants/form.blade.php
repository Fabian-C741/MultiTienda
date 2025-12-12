@php($tenant = $tenant ?? null)

@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="name">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', optional($tenant)->name) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="slug">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', optional($tenant)->slug) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <p class="text-xs text-slate-500">Si lo dejas vacío se generará automáticamente.</p>
    </div>
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700" for="domain">Dominio</label>
        <input id="domain" name="domain" type="text" value="{{ old('domain', optional($tenant)->domain) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <p class="text-xs text-slate-500">Ejemplo: tienda1.midominio.com (opcional).</p>
    </div>
    <div class="flex items-center space-x-2">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', optional($tenant)->is_active ?? true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        <label class="text-sm font-medium text-slate-700" for="is_active">Tienda activa</label>
    </div>
</div>

<div class="mt-8">
    <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Configuración de Base de Datos</h2>
    <div class="mt-4 grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="database">Nombre de base</label>
            <input id="database" name="database" type="text" value="{{ old('database', optional($tenant)->database) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="database_host">Host</label>
            <input id="database_host" name="database_host" type="text" value="{{ old('database_host', optional($tenant)->database_host ?? '127.0.0.1') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="database_port">Puerto</label>
            <input id="database_port" name="database_port" type="text" value="{{ old('database_port', optional($tenant)->database_port ?? '3306') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="database_username">Usuario</label>
            <input id="database_username" name="database_username" type="text" value="{{ old('database_username', optional($tenant)->database_username) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-medium text-slate-700" for="database_password">Contraseña</label>
            <input id="database_password" name="database_password" type="text" value="{{ old('database_password', optional($tenant)->database_password) }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
</div>

@if(!$tenant)
<div class="mt-8">
    <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Administrador de la Tienda</h2>
    <p class="mt-1 text-xs text-slate-500">Se creará un usuario administrador con acceso al panel de la tienda.</p>
    <div class="mt-4 grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="admin_name">Nombre del administrador</label>
            <input id="admin_name" name="admin[name]" type="text" value="{{ old('admin.name') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-700" for="admin_email">Email del administrador</label>
            <input id="admin_email" name="admin[email]" type="email" value="{{ old('admin.email') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-medium text-slate-700" for="admin_password">Contraseña del administrador</label>
            <input id="admin_password" name="admin[password]" type="password" value="{{ old('admin.password') }}" required minlength="8" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-slate-500">Mínimo 8 caracteres.</p>
        </div>
    </div>
</div>
@endif

<div class="mt-8 flex justify-end space-x-3">
    <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-50">Cancelar</a>
    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
</div>
