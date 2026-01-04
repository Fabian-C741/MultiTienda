<?php
/**
 * MultiTienda - Sistema Principal
 * Manejo directo de rutas sin redirecciones
 */

// Determinar la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

// Datos de demostración
$tenants = [
    ['id' => 1, 'name' => 'Tienda Electrónica', 'domain' => 'electronica.multitienda.com'],
    ['id' => 2, 'name' => 'Moda y Accesorios', 'domain' => 'moda.multitienda.com'],
    ['id' => 3, 'name' => 'Casa y Jardín', 'domain' => 'casa.multitienda.com']
];

// CSS común
$css = '
<style>
body { font-family: system-ui, sans-serif; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.hero { text-align: center; color: white; margin: 4rem 0; }
.hero h1 { font-size: 3rem; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.hero p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
.button-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 3rem 0; }
.feature-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 16px; padding: 2rem; text-align: center; color: white; transition: transform 0.3s; cursor: pointer; text-decoration: none; display: block; }
.feature-card:hover { transform: translateY(-8px); background: rgba(255,255,255,0.15); }
.feature-icon { font-size: 3rem; margin-bottom: 1rem; }
.feature-title { font-size: 1.5rem; margin-bottom: 1rem; font-weight: 600; }
.feature-desc { opacity: 0.8; line-height: 1.5; }
.navbar { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1rem 2rem; margin-bottom: 2rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; }
.logo { font-size: 1.5rem; font-weight: bold; color: white; }
.nav-links { display: flex; gap: 1rem; }
.nav-link { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; transition: background 0.3s; }
.nav-link:hover { background: rgba(255,255,255,0.1); }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 0; }
.stat-card { background: white; border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.stat-number { font-size: 2.5rem; font-weight: bold; color: #667eea; }
.stat-label { color: #6b7280; margin-top: 0.5rem; }
.tenant-list { background: white; border-radius: 12px; padding: 2rem; margin: 2rem 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.tenant-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #e5e7eb; }
.tenant-item:last-child { border-bottom: none; }
</style>';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MultiTienda - Plataforma Multi-Tenant</title>
    <?= $css ?>
</head>
<body>
    <div class="container">
        
        <?php if ($path !== '' && $path !== '/'): ?>
        <nav class="navbar">
            <div class="logo">🏪 MultiTienda</div>
            <div class="nav-links">
                <a href="/" class="nav-link">Inicio</a>
                <a href="/central" class="nav-link">Dashboard</a>
                <a href="/central/tenants" class="nav-link">Tiendas</a>
                <a href="/central/stats" class="nav-link">Estadísticas</a>
            </div>
        </nav>
        <?php endif; ?>

        <?php if ($path === '' || $path === '/'): ?>
        <!-- HOME PAGE -->
        <div class="hero">
            <h1>🏪 MultiTienda</h1>
            <p>Plataforma completa para gestionar múltiples tiendas online</p>
        </div>
        <div class="button-grid">
            <a href="/central" class="feature-card">
                <div class="feature-icon">📊</div>
                <div class="feature-title">Panel Central</div>
                <div class="feature-desc">Administra todas tus tiendas desde un dashboard unificado</div>
            </a>
            <a href="/central/tenants" class="feature-card">
                <div class="feature-icon">🏬</div>
                <div class="feature-title">Gestionar Tiendas</div>
                <div class="feature-desc">Crea, edita y administra todas tus tiendas virtuales</div>
            </a>
            <a href="/central/stats" class="feature-card">
                <div class="feature-icon">📈</div>
                <div class="feature-title">Estadísticas</div>
                <div class="feature-desc">Visualiza el rendimiento de todas tus tiendas</div>
            </a>
        </div>

        <?php elseif ($path === '/central'): ?>
        <!-- CENTRAL DASHBOARD -->
        <h1 style="color:white;">📊 Panel Central</h1>
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-number"><?= count($tenants) ?></div>
                <div class="stat-label">Tiendas Activas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Productos Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Ventas del Mes</div>
            </div>
        </div>

        <?php elseif ($path === '/central/tenants'): ?>
        <!-- TENANTS MANAGEMENT -->
        <h1 style="color:white;">🏬 Gestionar Tiendas</h1>
        <div class="tenant-list">
            <?php foreach ($tenants as $tenant): ?>
            <div class="tenant-item">
                <div>
                    <strong><?= htmlspecialchars($tenant['name']) ?></strong><br>
                    <small><?= htmlspecialchars($tenant['domain']) ?></small>
                </div>
                <div>
                    <a href="#" style="background:#667eea;color:white;padding:0.5rem 1rem;border-radius:6px;text-decoration:none;">Editar</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php elseif ($path === '/central/stats'): ?>
        <!-- STATS PAGE -->
        <h1 style="color:white;">📈 Estadísticas</h1>
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div class="stat-label">Uptime</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Usuarios Registrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$0</div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
        </div>

        <?php else: ?>
        <!-- 404 PAGE -->
        <div style="text-align:center;color:white;margin:4rem 0;">
            <h1>404 - Página no encontrada</h1>
            <p>La ruta solicitada no existe.</p>
            <a href="/" style="background:white;color:#667eea;padding:1rem 2rem;border-radius:8px;text-decoration:none;display:inline-block;margin-top:1rem;">Volver al inicio</a>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
