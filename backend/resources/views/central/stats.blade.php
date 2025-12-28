@extends('central.layout')

@section('title', 'Estadísticas - MultiTienda')

@section('header', 'Estadísticas del Sistema')

@section('content')
<div class="space-y-6">
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-store text-3xl text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Tiendas</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_tenants'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    <i class="fas fa-arrow-up"></i>
                                    <span class="sr-only">Increased by</span>
                                    {{ $stats['tenant_growth'] }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('central.tenants.index') }}" class="font-medium text-indigo-700 hover:text-indigo-900">Ver todas</a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tiendas Activas</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['active_tenants'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-gray-500">
                                    {{ number_format(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100, 1) }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Productos</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_products']) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-3xl text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Usuarios</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de crecimiento -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Crecimiento de Tiendas (Últimos 12 meses)</h3>
        <div class="h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <!-- Estadísticas por plan y estado -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Por plan -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Plan</h3>
            <div class="space-y-3">
                @foreach($stats['plans'] as $plan => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3 
                            @if($plan === 'basic') bg-gray-400
                            @elseif($plan === 'standard') bg-blue-400
                            @else bg-purple-400
                            @endif"></div>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($plan) }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">{{ $count }}</span>
                        <div class="w-24 h-2 bg-gray-200 rounded-full">
                            <div class="h-2 rounded-full 
                                @if($plan === 'basic') bg-gray-400
                                @elseif($plan === 'standard') bg-blue-400
                                @else bg-purple-400
                                @endif"
                                style="width: {{ ($count / max($stats['total_tenants'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Por estado -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Estados de Tiendas</h3>
            <div class="h-48">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top tiendas por actividad -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tiendas Más Activas</h3>
            @if($topTenants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tienda</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última actividad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topTenants as $tenant)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-store text-indigo-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $tenant->subdomain }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($tenant->plan === 'basic') bg-gray-100 text-gray-800
                                        @elseif($tenant->plan === 'standard') bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucfirst($tenant->plan ?? 'básico') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant->products_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant->users_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No hay datos de actividad disponibles</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas adicionales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-day text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tiendas creadas hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today_tenants'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-week text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Esta semana</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['week_tenants'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-alt text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Este mes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['month_tenants'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de crecimiento
const growthCtx = document.getElementById('growthChart').getContext('2d');
new Chart(growthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($stats['growth_labels']) !!},
        datasets: [{
            label: 'Tiendas Creadas',
            data: {!! json_encode($stats['growth_data']) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico de estado
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Activo', 'Suspendido', 'Inactivo'],
        datasets: [{
            data: [
                {{ $stats['active_tenants'] }},
                {{ $stats['suspended_tenants'] }},
                {{ $stats['inactive_tenants'] }}
            ],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection