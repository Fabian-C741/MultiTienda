@extends('central.layout')

@section('title', 'Crear Nueva Tienda - MultiTienda')

@section('header', 'Crear Nueva Tienda')

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('central.tenants.store') }}" class="space-y-8 bg-white shadow rounded-lg p-8">
        @csrf
        
        <!-- Información básica -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Tienda *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">El nombre público de la tienda</p>
                </div>

                <div>
                    <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdominio *</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}" required
                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('subdomain') border-red-500 @enderror">
                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            .multitienda.kcrsf.com
                        </span>
                    </div>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Solo letras minúsculas, números y guiones</p>
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Descripción breve de la tienda (opcional)</p>
            </div>
        </div>

        <!-- Información del propietario -->
        <div class="border-t border-gray-200 pt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Propietario de la Tienda</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                    <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('owner_name') border-red-500 @enderror">
                    @error('owner_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="owner_email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="owner_email" id="owner_email" value="{{ old('owner_email') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('owner_email') border-red-500 @enderror">
                    @error('owner_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Se creará automáticamente la cuenta de administrador</p>
                </div>
            </div>

            <div class="mt-6">
                <label for="owner_password" class="block text-sm font-medium text-gray-700">Contraseña *</label>
                <input type="password" name="owner_password" id="owner_password" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('owner_password') border-red-500 @enderror">
                @error('owner_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
            </div>
        </div>

        <!-- Configuración de la tienda -->
        <div class="border-t border-gray-200 pt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="plan" class="block text-sm font-medium text-gray-700">Plan</label>
                    <select name="plan" id="plan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Básico (Gratis)</option>
                        <option value="standard" {{ old('plan') === 'standard' ? 'selected' : '' }}>Estándar ($29/mes)</option>
                        <option value="premium" {{ old('plan') === 'premium' ? 'selected' : '' }}>Premium ($99/mes)</option>
                    </select>
                    @error('plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado Inicial</label>
                    <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <label for="currency" class="block text-sm font-medium text-gray-700">Moneda</label>
                <select name="currency" id="currency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                    <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - Libra Esterlina</option>
                    <option value="CAD" {{ old('currency') === 'CAD' ? 'selected' : '' }}>CAD - Dólar Canadiense</option>
                    <option value="AUD" {{ old('currency') === 'AUD' ? 'selected' : '' }}>AUD - Dólar Australiano</option>
                    <option value="MXN" {{ old('currency') === 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                    <option value="COP" {{ old('currency') === 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                    <option value="PEN" {{ old('currency') === 'PEN' ? 'selected' : '' }}>PEN - Sol Peruano</option>
                    <option value="CLP" {{ old('currency') === 'CLP' ? 'selected' : '' }}>CLP - Peso Chileno</option>
                    <option value="ARS" {{ old('currency') === 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                </select>
            </div>

            <div class="mt-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="setup_demo_data" name="setup_demo_data" type="checkbox" value="1" {{ old('setup_demo_data') ? 'checked' : '' }}
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="setup_demo_data" class="font-medium text-gray-700">Configurar datos de prueba</label>
                        <p class="text-gray-500">Incluye productos, categorías y configuración de ejemplo</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración avanzada -->
        <div class="border-t border-gray-200 pt-8">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="show_advanced" name="show_advanced" type="checkbox" 
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                           onchange="toggleAdvanced()">
                </div>
                <div class="ml-3 text-sm">
                    <label for="show_advanced" class="font-medium text-gray-700">Mostrar configuración avanzada</label>
                </div>
            </div>

            <div id="advanced_settings" class="hidden mt-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700">Zona Horaria</label>
                        <select name="timezone" id="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="America/New_York" {{ old('timezone', 'America/New_York') === 'America/New_York' ? 'selected' : '' }}>Nueva York (EST)</option>
                            <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Los Ángeles (PST)</option>
                            <option value="America/Mexico_City" {{ old('timezone') === 'America/Mexico_City' ? 'selected' : '' }}>Ciudad de México</option>
                            <option value="America/Bogota" {{ old('timezone') === 'America/Bogota' ? 'selected' : '' }}>Bogotá</option>
                            <option value="America/Lima" {{ old('timezone') === 'America/Lima' ? 'selected' : '' }}>Lima</option>
                            <option value="America/Santiago" {{ old('timezone') === 'America/Santiago' ? 'selected' : '' }}>Santiago</option>
                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>Londres</option>
                            <option value="Europe/Madrid" {{ old('timezone') === 'Europe/Madrid' ? 'selected' : '' }}>Madrid</option>
                        </select>
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700">Idioma</label>
                        <select name="language" id="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="es" {{ old('language', 'es') === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ old('language') === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="pt" {{ old('language') === 'pt' ? 'selected' : '' }}>Português</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="max_products" class="block text-sm font-medium text-gray-700">Límite de Productos</label>
                        <input type="number" name="max_products" id="max_products" value="{{ old('max_products', 1000) }}" min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Número máximo de productos (0 = ilimitado)</p>
                    </div>

                    <div>
                        <label for="max_storage" class="block text-sm font-medium text-gray-700">Límite de Almacenamiento (MB)</label>
                        <input type="number" name="max_storage" id="max_storage" value="{{ old('max_storage', 1000) }}" min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Espacio máximo para imágenes y archivos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-3 pt-8 border-t border-gray-200">
            <a href="{{ route('central.tenants.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancelar
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Crear Tienda
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Auto-generar subdominio basado en el nombre
document.getElementById('name').addEventListener('input', function() {
    const name = this.value.toLowerCase()
        .replace(/[^\w\s]/gi, '')
        .replace(/\s+/g, '-')
        .substring(0, 20);
    
    document.getElementById('subdomain').value = name;
});

// Validar subdominio en tiempo real
document.getElementById('subdomain').addEventListener('input', function() {
    const value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    this.value = value;
    
    // Verificar disponibilidad del subdominio
    if (value.length >= 3) {
        checkSubdomainAvailability(value);
    }
});

function checkSubdomainAvailability(subdomain) {
    // Aquí puedes agregar una llamada AJAX para verificar la disponibilidad
    // Por ahora, solo mostramos un mensaje de ejemplo
    const feedback = document.getElementById('subdomain-feedback');
    if (feedback) {
        feedback.remove();
    }
    
    // Crear elemento de feedback
    const feedbackElement = document.createElement('p');
    feedbackElement.id = 'subdomain-feedback';
    feedbackElement.className = 'mt-1 text-sm text-gray-500';
    feedbackElement.textContent = 'Verificando disponibilidad...';
    
    document.getElementById('subdomain').parentNode.appendChild(feedbackElement);
    
    // Simular verificación (en producción, esto sería una llamada AJAX)
    setTimeout(() => {
        feedbackElement.className = 'mt-1 text-sm text-green-600';
        feedbackElement.innerHTML = '<i class="fas fa-check mr-1"></i>Subdominio disponible';
    }, 1000);
}

function toggleAdvanced() {
    const checkbox = document.getElementById('show_advanced');
    const settings = document.getElementById('advanced_settings');
    
    if (checkbox.checked) {
        settings.classList.remove('hidden');
    } else {
        settings.classList.add('hidden');
    }
}

// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['name', 'subdomain', 'owner_name', 'owner_email', 'owner_password'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Por favor completa todos los campos requeridos.');
    }
});
</script>
@endsection