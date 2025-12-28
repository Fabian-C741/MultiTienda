@extends('central.layout')

@section('title', 'Gestión de Tiendas - MultiTienda')

@section('header', 'Gestión de Tiendas')

@section('content')
<div class="space-y-6">
    <!-- Botones de acción y filtros -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('central.tenants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Nueva Tienda
            </a>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="search" placeholder="Buscar tiendas..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <select id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="suspended">Suspendido</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>
    </div>

    <!-- Tabla de tiendas -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($tenants->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tienda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subdominio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última actividad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tenantsTableBody">
                        @foreach($tenants as $tenant)
                        <tr class="tenant-row" data-status="{{ $tenant->status }}" data-name="{{ strtolower($tenant->name) }}" data-subdomain="{{ strtolower($tenant->subdomain) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_tenants[]" value="{{ $tenant->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded tenant-checkbox">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <i class="fas fa-store text-indigo-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($tenant->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="https://{{ $tenant->subdomain }}.multitienda.kcrsf.com" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    {{ $tenant->subdomain }}
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($tenant->plan ?? 'básico') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tenant->products_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tenant->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('central.tenants.show', $tenant) }}" class="text-indigo-600 hover:text-indigo-900" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('central.tenants.edit', $tenant) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($tenant->status === 'active')
                                        <button onclick="suspendTenant({{ $tenant->id }})" class="text-orange-600 hover:text-orange-900" title="Suspender">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @else
                                        <button onclick="activateTenant({{ $tenant->id }})" class="text-green-600 hover:text-green-900" title="Activar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    <button onclick="deleteTenant({{ $tenant->id }}, '{{ $tenant->name }}')" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $tenants->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-store text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay tiendas</h3>
                <p class="text-gray-500 mb-4">Comienza creando tu primera tienda multitienda.</p>
                <a href="{{ route('central.tenants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primera Tienda
                </a>
            </div>
        @endif
    </div>

    <!-- Acciones masivas -->
    <div id="bulkActions" class="hidden bg-white p-4 rounded-lg shadow">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span id="selectedCount" class="text-sm text-gray-500 mr-4">0 tiendas seleccionadas</span>
            </div>
            <div class="flex space-x-2">
                <button onclick="bulkAction('activate')" class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200">
                    <i class="fas fa-play mr-1"></i> Activar
                </button>
                <button onclick="bulkAction('suspend')" class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200">
                    <i class="fas fa-pause mr-1"></i> Suspender
                </button>
                <button onclick="bulkAction('delete')" class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                </button>
            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('statusFilter');
    const selectAllCheckbox = document.getElementById('selectAll');
    const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox');
    const bulkActionsDiv = document.getElementById('bulkActions');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Funcionalidad de búsqueda y filtrado
    function filterTenants() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('.tenant-row');

        rows.forEach(row => {
            const name = row.dataset.name;
            const subdomain = row.dataset.subdomain;
            const status = row.dataset.status;
            
            const matchesSearch = name.includes(searchTerm) || subdomain.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTenants);
    statusFilter.addEventListener('change', filterTenants);

    // Funcionalidad de selección masiva
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActionsDiv.classList.remove('hidden');
            selectedCountSpan.textContent = `${count} tienda${count > 1 ? 's' : ''} seleccionada${count > 1 ? 's' : ''}`;
        } else {
            bulkActionsDiv.classList.add('hidden');
        }
        
        selectAllCheckbox.indeterminate = count > 0 && count < tenantCheckboxes.length;
        selectAllCheckbox.checked = count === tenantCheckboxes.length;
    }

    selectAllCheckbox.addEventListener('change', function() {
        tenantCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    tenantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

function suspendTenant(id) {
    showConfirmModal(
        'Suspender tienda',
        '¿Estás seguro de que quieres suspender esta tienda? Los usuarios no podrán acceder a ella.',
        () => {
            fetch(`/central/tenants/${id}/suspend`, {
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

function activateTenant(id) {
    showConfirmModal(
        'Activar tienda',
        '¿Estás seguro de que quieres activar esta tienda?',
        () => {
            fetch(`/central/tenants/${id}/activate`, {
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

function deleteTenant(id, name) {
    showConfirmModal(
        'Eliminar tienda',
        `¿Estás seguro de que quieres eliminar permanentemente la tienda "${name}"? Esta acción no se puede deshacer.`,
        () => {
            fetch(`/central/tenants/${id}`, {
                method: 'DELETE',
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

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) return;

    let title, message, url, method = 'POST';
    
    switch (action) {
        case 'activate':
            title = 'Activar tiendas';
            message = `¿Estás seguro de que quieres activar ${ids.length} tienda${ids.length > 1 ? 's' : ''}?`;
            url = '/central/tenants/bulk-activate';
            break;
        case 'suspend':
            title = 'Suspender tiendas';
            message = `¿Estás seguro de que quieres suspender ${ids.length} tienda${ids.length > 1 ? 's' : ''}?`;
            url = '/central/tenants/bulk-suspend';
            break;
        case 'delete':
            title = 'Eliminar tiendas';
            message = `¿Estás seguro de que quieres eliminar permanentemente ${ids.length} tienda${ids.length > 1 ? 's' : ''}? Esta acción no se puede deshacer.`;
            url = '/central/tenants/bulk-delete';
            method = 'DELETE';
            break;
    }

    showConfirmModal(title, message, () => {
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
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