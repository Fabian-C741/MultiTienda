<?php
/**
 * 🏪 MultiTienda Pro - Sistema Completo Multi-Tenant Moderno
 */

require_once 'auth.php';
require_once 'storage.php';
require_once 'super-admin-functions.php';

// Función para mostrar el layout base profesional
function showLayout($title, $user, $content) {
    $currentPage = $_SERVER['REQUEST_URI'] ?? '';
    $userInitial = strtoupper(substr($user['name'], 0, 1));
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?> - MultiTienda Pro</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.js"></script>
        <link rel="stylesheet" href="enterprise-design.css">
        <script src="analytics-engine.js"></script>
        <script src="component-system.js"></script>
    </head>
    <body>
        <div class="app">
            <aside class="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo">
                        <i class="fas fa-store"></i>
                        <?= $user['role'] === 'super_admin' ? 'MultiTienda Pro' : ($user['store_name'] ?? 'Mi Tienda') ?>
                    </div>
                </div>
                
                <nav class="sidebar-nav">
                    <?php if ($user['role'] === 'super_admin'): ?>
                        <div class="nav-section">
                            <div class="nav-section-title">Panel Principal</div>
                            <a href="/super-admin" class="nav-item <?= strpos($currentPage, '/super-admin') === 0 && strlen($currentPage) <= 12 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                Dashboard
                            </a>
                            <a href="/super-admin/admins" class="nav-item <?= strpos($currentPage, '/super-admin/admins') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users-cog"></i>
                                Admin Principales
                            </a>
                        </div>
                        
                        <div class="nav-section">
                            <div class="nav-section-title">Gestión</div>
                            <a href="/super-admin/stores" class="nav-item <?= strpos($currentPage, '/super-admin/stores') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-store-alt"></i>
                                Todas las Tiendas
                            </a>
                            <a href="/super-admin/analytics" class="nav-item <?= strpos($currentPage, '/super-admin/analytics') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-line"></i>
                                Analíticas
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="nav-section">
                            <div class="nav-section-title">Mi Tienda</div>
                            <a href="/admin" class="nav-item <?= $currentPage === '/admin' || $currentPage === '/admin/' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                Dashboard
                            </a>
                            <a href="/admin/products" class="nav-item <?= strpos($currentPage, '/admin/products') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-box"></i>
                                Productos
                            </a>
                            <a href="/admin/orders" class="nav-item <?= strpos($currentPage, '/admin/orders') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                Pedidos
                            </a>
                        </div>
                        
                        <div class="nav-section">
                            <div class="nav-section-title">Configuración</div>
                            <a href="/admin/store-design" class="nav-item <?= strpos($currentPage, '/admin/store-design') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-palette"></i>
                                Diseño de Tienda
                            </a>
                            <a href="/admin/analytics" class="nav-item <?= strpos($currentPage, '/admin/analytics') === 0 ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                Analíticas
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </aside>
            
            <main class="main-content">
                <header class="top-header">
                    <h1 class="header-title"><?= $title ?></h1>
                    <div class="header-actions">
                        <div class="user-menu">
                            <div class="user-avatar"><?= $userInitial ?></div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem;"><?= $user['name'] ?></div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></div>
                            </div>
                            <a href="/logout" style="color: var(--error); text-decoration: none; margin-left: 0.5rem;">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </header>
                
                <div class="content-area">
                    <?= $content ?>
                </div>
            </main>
        </div>
        <script>
        // Inicializar dashboard enterprise cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar animaciones de métricas
            if (window.realTimeMetrics) {
                // Animar contadores en las metric cards
                document.querySelectorAll('.metric-value').forEach((element, index) => {
                    const value = parseInt(element.textContent.replace(/[^0-9]/g, '')) || 0;
                    if (element.textContent.includes('$')) {
                        window.realTimeMetrics.animateCurrency(element.id || 'metric-' + index, value, 1500);
                    } else {
                        window.realTimeMetrics.animateCounter(element.id || 'metric-' + index, value, 1500);
                    }
                });
                
                // Iniciar actualizaciones en tiempo real
                setTimeout(() => {
                    window.realTimeMetrics.startRealTimeUpdates();
                }, 3000);
            }
            
            // Inicializar gráficas si estamos en la página de analytics
            if (window.location.pathname.includes('analytics') && window.analyticsEngine) {
                setTimeout(() => {
                    initializeAnalyticsCharts();
                }, 500);
            }
        });
        
        function initializeAnalyticsCharts() {
            // Crear gráfica de ventas
            const salesCanvas = document.getElementById('salesChart');
            if (salesCanvas) {
                window.analyticsEngine.createSalesChart('salesChart', 
                    window.analyticsEngine.generateMockData('sales')
                );
            }
            
            // Crear gráfica de categorías
            const categoryCanvas = document.getElementById('categoryChart');
            if (categoryCanvas) {
                window.analyticsEngine.createCategoryChart('categoryChart', 
                    window.analyticsEngine.generateMockData('categories')
                );
            }
            
            // Crear gráfica de rendimiento de tiendas
            const storeCanvas = document.getElementById('storeChart');
            if (storeCanvas) {
                window.analyticsEngine.createStorePerformanceChart('storeChart', 
                    window.analyticsEngine.generateMockData('stores')
                );
            }
            
            // Crear gráfica de actividad
            const activityCanvas = document.getElementById('activityChart');
            if (activityCanvas) {
                window.analyticsEngine.createUserActivityChart('activityChart', 
                    window.analyticsEngine.generateMockData('activity')
                );
            }
        }
        
        // Función para mostrar notificaciones de éxito
        function showSuccessToast(message) {
            if (window.showToast) {
                window.showToast(message, 'success', { duration: 4000 });
            }
        }
        
        // Función para mostrar errores
        function showErrorToast(message) {
            if (window.showToast) {
                window.showToast(message, 'error', { duration: 6000 });
            }
        }
        
        // Configurar tooltips interactivos
        document.querySelectorAll('[title]').forEach(element => {
            element.addEventListener('mouseenter', function(e) {
                // Crear tooltip personalizado
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = this.getAttribute('title');
                tooltip.style.cssText = `
                    position: absolute;
                    background: rgba(0, 0, 0, 0.9);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.875rem;
                    z-index: 1000;
                    pointer-events: none;
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                `;
                document.body.appendChild(tooltip);
                
                // Posicionar tooltip
                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + 'px';
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                
                // Remover title para evitar tooltip nativo
                this.setAttribute('data-title', this.getAttribute('title'));
                this.removeAttribute('title');
            });
            
            element.addEventListener('mouseleave', function() {
                // Restaurar title y remover tooltip
                if (this.getAttribute('data-title')) {
                    this.setAttribute('title', this.getAttribute('data-title'));
                    this.removeAttribute('data-title');
                }
                document.querySelectorAll('.custom-tooltip').forEach(t => t.remove());
            });
        });
        </script>
    </body>
    </html>
    <?php
}

// Determinar la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

// Página principal - Landing profesional
if (empty($path) || $path === '/') {
    if (auth()->isLoggedIn()) {
        $user = auth()->getUser();
        if ($user['role'] === 'super_admin') {
            header('Location: /super-admin');
        } else {
            header('Location: /admin');
        }
        exit;
    }
    
    include 'landing.html';
    exit;
}

// Página de login
if ($path === '/login') {
    if (auth()->isLoggedIn()) {
        $user = auth()->getUser();
        header('Location: ' . ($user['role'] === 'super_admin' ? '/super-admin' : '/admin'));
        exit;
    }
    
    $error = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (auth()->login($email, $password)) {
            $user = auth()->getUser();
            header('Location: ' . ($user['role'] === 'super_admin' ? '/super-admin' : '/admin'));
            exit;
        } else {
            $error = 'Credenciales incorrectas';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - MultiTienda Pro</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <?php include 'modern-css.php'; ?>
        <style>
            .login-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                padding: 2rem;
            }
            .login-card {
                background: white;
                border-radius: var(--radius-lg);
                padding: 3rem;
                box-shadow: var(--shadow-xl);
                width: 100%;
                max-width: 400px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .login-header {
                text-align: center;
                margin-bottom: 2rem;
            }
            .login-logo {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            .login-title {
                font-size: 1.75rem;
                font-weight: 700;
                color: var(--gray-900);
                margin-bottom: 0.5rem;
            }
            .login-subtitle {
                color: var(--gray-500);
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">🏪</div>
                    <h1 class="login-title">MultiTienda Pro</h1>
                    <p class="login-subtitle">Accede a tu panel de administración</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-input" required value="<?= $_POST['email'] ?? '' ?>" placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-input" required placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--gray-200);">
                    <p style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 1rem;"><strong>Cuentas de prueba:</strong></p>
                    <div style="font-size: 0.8rem; color: var(--gray-400); display: flex; flex-direction: column; gap: 0.5rem;">
                        <div><strong>Super Admin:</strong> admin@multitienda.com / admin123</div>
                        <div><strong>Admin Tienda:</strong> tienda1@demo.com / demo123</div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Cerrar sesión
if ($path === '/logout') {
    auth()->logout();
    header('Location: /');
    exit;
}

// Panel Super Admin
if (strpos($path, '/super-admin') === 0) {
    auth()->requireRole('super_admin');
    $user = auth()->getUser();
    
    if ($path === '/super-admin') {
        // Dashboard principal del Super Admin
        $admins = storage()->find('users', ['role' => 'admin']);
        $stores = storage()->find('stores');
        $totalSales = 0;
        foreach ($stores as $store) {
            $totalSales += ($store['total_sales'] ?? 0);
        }
        
        $content = '
        <div class="dashboard-grid">
            <div class="metric-card">
                <div class="metric-icon primary">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="metric-value">'.count($admins).'</div>
                <div class="metric-label">Admin Principales</div>
                <div class="metric-trend up">
                    <i class="fas fa-arrow-up"></i>
                    +12% este mes
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon success">
                    <i class="fas fa-store"></i>
                </div>
                <div class="metric-value">'.count($stores).'</div>
                <div class="metric-label">Tiendas Activas</div>
                <div class="metric-trend up">
                    <i class="fas fa-arrow-up"></i>
                    +8% este mes
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-value">$'.number_format($totalSales, 0).'</div>
                <div class="metric-label">Ventas Totales</div>
                <div class="metric-trend up">
                    <i class="fas fa-arrow-up"></i>
                    +24% este mes
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-value">'.date('H:i').'</div>
                <div class="metric-label">Hora del Sistema</div>
                <div class="metric-trend">
                    <i class="fas fa-globe"></i>
                    '.date('d/m/Y').'
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-users-cog"></i>
                    Administradores Principales
                </h2>
            </div>
            <div class="card-content">';
                        
        if (!empty($admins)) {
            $content .= '<table class="table">
                <thead>
                    <tr>
                        <th>Administrador</th>
                        <th>Email</th>
                        <th>Tienda Asignada</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';
                
            foreach ($admins as $admin) {
                $adminStore = storage()->findOne('stores', ['admin_id' => $admin['id']]);
                $content .= '<tr>
                    <td><strong>'.$admin['name'].'</strong></td>
                    <td>'.$admin['email'].'</td>
                    <td>'.($adminStore ? $adminStore['name'] : '<span class="badge badge-warning">Sin tienda</span>').'</td>
                    <td><span class="badge badge-success">Activo</span></td>
                    <td>
                        <a href="/super-admin/admin/'.$admin['id'].'" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.75rem;">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>';
            }
            
            $content .= '</tbody></table>';
        } else {
            $content .= '<div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h3 class="empty-state-title">No hay administradores</h3>
                <p class="empty-state-description">Comienza creando tu primer administrador principal para gestionar tiendas</p>
                <a href="/super-admin/create-admin" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Administrador
                </a>
            </div>';
        }
        
        $content .= '
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-rocket"></i>
                    Accesos Rápidos
                </h2>
            </div>
            <div class="card-content">
                <div class="dashboard-grid">
                    <a href="/super-admin/create-admin" class="btn btn-primary" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-user-plus" style="font-size: 2rem;"></i>
                        <span>Crear Administrador</span>
                    </a>
                    
                    <a href="/super-admin/stores" class="btn btn-success" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-store-alt" style="font-size: 2rem;"></i>
                        <span>Ver Todas las Tiendas</span>
                    </a>
                    
                    <a href="/super-admin/analytics" class="btn btn-secondary" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-chart-line" style="font-size: 2rem;"></i>
                        <span>Analíticas Avanzadas</span>
                    </a>
                </div>
            </div>
        </div>';
        
        showLayout('Dashboard Principal', $user, $content);
        exit;
    }
    
    // Crear nuevo administrador
    if ($path === '/super-admin/create-admin') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $phone = $_POST['phone'] ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                $content = '<div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    Todos los campos obligatorios deben estar completos
                </div>' . renderSuperAdminCreateForm();
            } else {
                // Verificar si el email ya existe
                $existingUser = storage()->findOne('users', ['email' => $email]);
                if ($existingUser) {
                    $content = '<div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        Ya existe un usuario con ese correo electrónico
                    </div>' . renderSuperAdminCreateForm();
                } else {
                    // Crear el nuevo administrador
                    $adminId = storage()->insert('users', [
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'admin',
                        'phone' => $phone,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $content = '<div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>¡Administrador creado exitosamente!</strong><br>
                            Credenciales: '.$email.' / '.$password.'<br>
                            <a href="/super-admin" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-arrow-left"></i> Volver al Dashboard
                            </a>
                        </div>
                    </div>';
                }
            }
        } else {
            $content = renderSuperAdminCreateForm();
        }
        
        showLayout('Crear Administrador', $user, $content);
        exit;
    }
    
    // Ver todas las tiendas
    if ($path === '/super-admin/stores') {
        $stores = storage()->find('stores');
        $content = renderStoresList($stores);
        
        showLayout('Todas las Tiendas', $user, $content);
        exit;
    }
    
    // Analíticas avanzadas
    if ($path === '/super-admin/analytics') {
        $stores = storage()->find('stores');
        $admins = storage()->find('users', ['role' => 'admin']);
        
        $content = renderAnalyticsDashboard($stores, $admins);
        
        showLayout('Analíticas Avanzadas', $user, $content);
        exit;
    }
}

// Panel Admin
if (strpos($path, '/admin') === 0) {
    auth()->requireRole('admin');
    $user = auth()->getUser();
    
    // Buscar la tienda del admin
    $store = storage()->findOne('stores', ['admin_id' => $user['id']]);
    
    if (!$store) {
        // Si no tiene tienda, permitir crear una
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_store'])) {
            $storeName = $_POST['store_name'] ?? '';
            $storeDescription = $_POST['store_description'] ?? '';
            
            if (!empty($storeName)) {
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $storeName));
                $storeId = storage()->insert('stores', [
                    'name' => $storeName,
                    'slug' => $slug,
                    'description' => $storeDescription,
                    'admin_id' => $user['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'total_sales' => 0
                ]);
                header('Location: /admin');
                exit;
            }
        }
        
        $content = '
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-store"></i>
                    Crear Mi Tienda
                </h2>
            </div>
            <div class="card-content">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>Bienvenido <strong>'.$user['name'].'</strong>. Para comenzar, necesitas crear tu tienda online.</div>
                </div>
                
                <form method="POST" style="max-width: 600px;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-store"></i> Nombre de tu Tienda
                        </label>
                        <input type="text" name="store_name" class="form-input" required placeholder="Mi Tienda Online">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Descripción (Opcional)
                        </label>
                        <textarea name="store_description" class="form-textarea" rows="3" placeholder="Describe tu tienda y los productos que vendes..."></textarea>
                    </div>
                    
                    <button type="submit" name="create_store" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Mi Tienda
                    </button>
                </form>
            </div>
        </div>';
        
        showLayout('Crear Tienda', $user, $content);
        exit;
    }
    
    if ($path === '/admin' || $path === '/admin/') {
        // Dashboard principal del admin
        $products = storage()->find('products', ['store_id' => $store['id']]);
        $orders = storage()->find('orders', ['store_id' => $store['id']]);
        $totalSales = $store['total_sales'] ?? 0;
        
        $recentOrders = array_slice($orders, -5);
        
        $content = '
        <div class="dashboard-grid">
            <div class="metric-card">
                <div class="metric-icon primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="metric-value">'.count($products).'</div>
                <div class="metric-label">Productos</div>
                <div class="metric-trend">
                    <i class="fas fa-plus"></i>
                    <a href="/admin/products/create" style="color: inherit; text-decoration: none;">Agregar</a>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-value">'.count($orders).'</div>
                <div class="metric-label">Pedidos Totales</div>
                <div class="metric-trend up">
                    <i class="fas fa-arrow-up"></i>
                    Este mes
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="metric-value">$'.number_format($totalSales, 0).'</div>
                <div class="metric-label">Ventas Totales</div>
                <div class="metric-trend up">
                    <i class="fas fa-chart-line"></i>
                    Revenue
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon warning">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="metric-value">
                    <a href="/tienda/'.$store['slug'].'" target="_blank" style="color: inherit; text-decoration: none; font-size: 1rem;">
                        Ver Tienda
                    </a>
                </div>
                <div class="metric-label">Página Pública</div>
                <div class="metric-trend">
                    <i class="fas fa-external-link-alt"></i>
                    Live
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-shopping-cart"></i>
                    Pedidos Recientes
                </h2>
            </div>
            <div class="card-content">';
            
        if (!empty($recentOrders)) {
            $content .= '<table class="table">
                <thead>
                    <tr>
                        <th>Pedido #</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>';
                
            foreach ($recentOrders as $order) {
                $content .= '<tr>
                    <td><strong>#'.($order['id'] ?? 'N/A').'</strong></td>
                    <td>'.$order['customer_name'].'</td>
                    <td><strong>$'.number_format($order['total'], 2).'</strong></td>
                    <td><span class="badge badge-success">'.($order['status'] ?? 'Nuevo').'</span></td>
                    <td>'.date('d/m/Y', strtotime($order['created_at'] ?? 'now')).'</td>
                </tr>';
            }
            
            $content .= '</tbody></table>
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/admin/orders" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Todos los Pedidos
                </a>
            </div>';
        } else {
            $content .= '<div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="empty-state-title">No hay pedidos aún</h3>
                <p class="empty-state-description">Los pedidos de tu tienda aparecerán aquí cuando los clientes realicen compras</p>
                <a href="/tienda/'.$store['slug'].'" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Ver Mi Tienda
                </a>
            </div>';
        }
        
        $content .= '
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-rocket"></i>
                    Accesos Rápidos
                </h2>
            </div>
            <div class="card-content">
                <div class="dashboard-grid">
                    <a href="/admin/products/create" class="btn btn-primary" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-plus" style="font-size: 2rem;"></i>
                        <span>Agregar Producto</span>
                    </a>
                    
                    <a href="/admin/orders" class="btn btn-success" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-list-alt" style="font-size: 2rem;"></i>
                        <span>Gestionar Pedidos</span>
                    </a>
                    
                    <a href="/admin/store-design" class="btn btn-secondary" style="padding: 2rem; text-decoration: none; text-align: center; display: flex; flex-direction: column; gap: 1rem; height: auto;">
                        <i class="fas fa-palette" style="font-size: 2rem;"></i>
                        <span>Personalizar Tienda</span>
                    </a>
                </div>
            </div>
        </div>';
        
        showLayout('Dashboard - '.$store['name'], $user, $content);
        exit;
    }
    
    // Gestión de productos
    if (strpos($path, '/admin/products') === 0) {
        if ($path === '/admin/products/create') {
            // Lógica para crear producto
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'] ?? '';
                $price = floatval($_POST['price'] ?? 0);
                $description = $_POST['description'] ?? '';
                $stock = intval($_POST['stock'] ?? 0);
                
                if (!empty($name) && $price > 0) {
                    storage()->insert('products', [
                        'name' => $name,
                        'price' => $price,
                        'description' => $description,
                        'stock' => $stock,
                        'store_id' => $store['id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    header('Location: /admin/products?success=1');
                    exit;
                }
            }
            
            $content = '
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-plus"></i>
                        Agregar Nuevo Producto
                    </h2>
                </div>
                <div class="card-content">
                    <form method="POST" style="max-width: 800px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                            <div class="form-group">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" name="name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Precio ($)</label>
                                <input type="number" name="price" class="form-input" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-textarea" rows="4"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stock Disponible</label>
                            <input type="number" name="stock" class="form-input" value="0">
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Producto
                            </button>
                            <a href="/admin/products" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>';
            
            showLayout('Agregar Producto - '.$store['name'], $user, $content);
            exit;
        }
        
        // Lista de productos
        $products = storage()->find('products', ['store_id' => $store['id']]);
        $success = isset($_GET['success']) ? 'Producto agregado exitosamente' : null;
        
        $content = '';
        
        if ($success) {
            $content .= '<div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                '.$success.'
            </div>';
        }
        
        $content .= '
        <div class="content-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">
                    <i class="fas fa-box"></i>
                    Mis Productos ('.count($products).')
                </h2>
                <a href="/admin/products/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Agregar Producto
                </a>
            </div>
            <div class="card-content">';
            
        if (!empty($products)) {
            $content .= '<div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
            foreach ($products as $product) {
                $content .= '<tr>
                    <td>
                        <div>
                            <div style="font-weight: 600;">'.$product['name'].'</div>
                            <div style="font-size: 0.875rem; color: var(--gray-500);">'.substr($product['description'], 0, 50).'...</div>
                        </div>
                    </td>
                    <td><strong>$'.number_format($product['price'], 2).'</strong></td>
                    <td><span class="badge '.($product['stock'] > 0 ? 'badge-success' : 'badge-warning').'">'.$product['stock'].'</span></td>
                    <td><span class="badge badge-success">Activo</span></td>
                    <td>'.date('d/m/Y', strtotime($product['created_at'])).'</td>
                    <td>
                        <button class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem;" onclick="alert(\'Editar funcionalidad en desarrollo\')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>';
            }
            
            $content .= '</tbody></table></div>';
        } else {
            $content .= '<div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="empty-state-title">No hay productos</h3>
                <p class="empty-state-description">Agrega tu primer producto para comenzar a vender</p>
                <a href="/admin/products/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Agregar Primer Producto
                </a>
            </div>';
        }
        
        $content .= '
            </div>
        </div>';
        
        showLayout('Productos - '.$store['name'], $user, $content);
        exit;
    }
    
    // Otras rutas del admin (órdenes, diseño, etc.)
    $content = '<div class="content-card"><div class="card-content"><h2>Funcionalidad en desarrollo</h2><p>Esta sección estará disponible próximamente.</p><a href="/admin" class="btn btn-primary">Volver al Dashboard</a></div></div>';
    showLayout('Administración - '.$store['name'], $user, $content);
    exit;
}

// Tienda pública
if (preg_match('/^\/tienda\/([^\/]+)/', $path, $matches)) {
    $slug = $matches[1];
    $store = storage()->findOne('stores', ['slug' => $slug]);
    
    if (!$store) {
        http_response_code(404);
        echo "<h1>Tienda no encontrada</h1>";
        exit;
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $store['name'] ?></title>
        <?php include 'modern-css.php'; ?>
    </head>
    <body>
        <div class="content-card">
            <div class="card-content">
                <h1><?= $store['name'] ?></h1>
                <p><?= $store['description'] ?? 'Bienvenido a nuestra tienda online' ?></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 404 - Página no encontrada
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - MultiTienda Pro</title>
    <?php include 'modern-css.php'; ?>
</head>
<body>
    <div class="content-area">
        <div class="empty-state" style="padding: 4rem 2rem;">
            <div class="empty-state-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="empty-state-title">404 - Página no encontrada</h1>
            <p class="empty-state-description">La página que buscas no existe o ha sido movida.</p>
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>