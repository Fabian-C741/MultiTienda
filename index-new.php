<?php
/**
 * 🏪 MultiTienda Pro - Sistema Completo Multi-Tenant Moderno
 */

require_once 'auth.php';
require_once 'storage.php';

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
        <?php include 'modern-css.php'; ?>
    </head>
    <body>
        <div class="app-layout">
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
    
    // Resto del código para otras rutas de super-admin...
    showLayout('Super Admin', $user, '<div class="content-card"><div class="card-content"><h2>Funcionalidad en desarrollo</h2><p>Esta sección estará disponible próximamente.</p></div></div>');
    exit;
}

// Panel Admin
if (strpos($path, '/admin') === 0) {
    auth()->requireRole('admin');
    $user = auth()->getUser();
    
    showLayout('Dashboard Admin', $user, '<div class="content-card"><div class="card-content"><h2>Panel Administrador</h2><p>Bienvenido '.$user['name'].'</p></div></div>');
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