@extends('central.layout')

@section('title', $tenant->name . ' - Detalles de Tienda')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $tenant->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('central.tenants.edit', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-edit mr-2"></i>
                Editar
            </a>
            <a href="https://{{ $tenant->subdomain }}.multitienda.kcrsf.com" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-external-link-alt mr-2"></i>
                Ver Tienda
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Estado y acciones rápidas -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-store text-indigo-600 text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h2>
                    <p class="text-gray-500">{{ $tenant->description ?? 'Sin descripción' }}</p>
                    <div class="flex items-center mt-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($tenant->status === 'active') bg-green-100 text-green-800
                            @elseif($tenant->status === 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($tenant->status === 'active') Activo
                            @elseif($tenant->status === 'suspended') Suspendido
                            @else Inactivo
                            @endif
                        </span>
                        <span class="ml-3 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($tenant->plan ?? 'básico') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col space-y-2">
                @if($tenant->status === 'active')
                    <button onclick="suspendTenant()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                        <i class="fas fa-pause mr-2"></i>
                        Suspender
                    </button>
                @else
                    <button onclick="activateTenant()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200">
                        <i class="fas fa-play mr-2"></i>
                        Activar
                    </button>
                @endif
                <button onclick="deleteTenant()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                    <i class="fas fa-trash mr-2"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información general -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Subdominio</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="https://{{ $tenant->subdomain }}.multitienda.kcrsf.com" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                            {{ $tenant->subdomain }}.multitienda.kcrsf.com
                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </dd>
                </div>
                @if($tenant->domain)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dominio Personalizado</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="https://{{ $tenant->domain }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                            {{ $tenant->domain }}
                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Plan</dt>
                    <dd class="text-sm text-gray-900">{{ ucfirst($tenant->plan ?? 'básico') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($tenant->status === 'active') bg-green-100 text-green-800
                            @elseif($tenant->status === 'suspended') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($tenant->status === 'active') Activo
                            @elseif($tenant->status === 'suspended') Suspendido
                            @else Inactivo
                            @endif
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Creado</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Moneda</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->getSetting('currency', 'USD') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Zona Horaria</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->getSetting('timezone', 'America/New_York') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Idioma</dt>
                    <dd class="text-sm text-gray-900">
                        @switch($tenant->getSetting('language', 'es'))
                            @case('es') Español @break
                            @case('en') English @break
                            @case('fr') Français @break
                            @case('pt') Português @break
                            @default {{ $tenant->getSetting('language', 'es') }}
                        @endswitch
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Límite de Productos</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $tenant->getSetting('max_products', 1000) == 0 ? 'Ilimitado' : number_format($tenant->getSetting('max_products', 1000)) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Límite de Almacenamiento</dt>
                    <dd class="text-sm text-gray-900">{{ number_format($tenant->getSetting('max_storage', 1000)) }} MB</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Usuarios</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $tenant->users->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-box text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Productos</p>
                    <p class="text-2xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Órdenes</p>
                    <p class="text-2xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-dollar-sign text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ventas</p>
                    <p class="text-2xl font-semibold text-gray-900">$0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios de la Tienda</h3>
            @if($tenant->users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último acceso</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tenant->users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-600">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $user->role ?? 'Usuario' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No hay usuarios registrados en esta tienda</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Confirmar acción</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">¿Estás seguro de que quieres realizar esta acción?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmButton" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Confirmar
                    </button>
                    <button onclick="closeModal()" class="ml-3 px-4 py-2 bg-gray-300 text-gray-900 text-base font-medium rounded-md w-24 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function suspendTenant() {
    showConfirmModal(
        'Suspender tienda',
        '¿Estás seguro de que quieres suspender esta tienda? Los usuarios no podrán acceder a ella.',
        () => {
            fetch(`/central/tenants/{{ $tenant->id }}/suspend`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    );
}

function activateTenant() {
    showConfirmModal(
        'Activar tienda',
        '¿Estás seguro de que quieres activar esta tienda?',
        () => {
            fetch(`/central/tenants/{{ $tenant->id }}/activate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    );
}

function deleteTenant() {
    showConfirmModal(
        'Eliminar tienda',
        `¿Estás seguro de que quieres eliminar permanentemente la tienda "{{ $tenant->name }}"? Esta acción no se puede deshacer y se eliminarán todos los datos asociados.`,
        () => {
            fetch(`/central/tenants/{{ $tenant->id }}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.message) {
                    window.location.href = '/central/tenants';
                }
            });
        }
    );
}

function showConfirmModal(title, message, onConfirm) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('confirmButton').onclick = () => {
        onConfirm();
        closeModal();
    };
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}
</script>
@endsection