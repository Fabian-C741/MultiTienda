<?php
/**
 * 🏪 MultiTienda - Sistema Completo de E-commerce Multi-Tenant
 * Super Admin > Admins > Tiendas > Clientes
 */

require_once 'auth.php';
require_once 'storage.php';

// Determinar la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

// CSS base
$css = '
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: system-ui, -apple-system, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.auth-container { max-width: 400px; margin: 50px auto; background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.btn { background: #667eea; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; text-decoration: none; display: inline-block; cursor: pointer; font-size: 1rem; transition: all 0.3s; }
.btn:hover { background: #5a67d8; transform: translateY(-2px); }
.btn-danger { background: #ef4444; }
.btn-danger:hover { background: #dc2626; }
.btn-success { background: #10b981; }
.btn-success:hover { background: #059669; }
.hero { text-align: center; color: white; margin: 4rem 0; }
.hero h1 { font-size: 3rem; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.hero p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
.navbar { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1rem 2rem; margin-bottom: 2rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; }
.logo { font-size: 1.5rem; font-weight: bold; color: white; text-decoration: none; }
.nav-links { display: flex; gap: 1rem; align-items: center; }
.nav-link { color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; transition: background 0.3s; }
.nav-link:hover { background: rgba(255,255,255,0.1); }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 0; }
.stat-card { background: white; border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.stat-number { font-size: 2.5rem; font-weight: bold; color: #667eea; }
.stat-label { color: #6b7280; margin-top: 0.5rem; }
.card { background: white; border-radius: 12px; padding: 2rem; margin: 2rem 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
.table th { background: #f8fafc; font-weight: 600; }
.badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.alert { padding: 1rem; border-radius: 6px; margin: 1rem 0; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
</style>';

// Manejo de rutas
if ($path === '/login' || $path === '/login.php') {
    // Página de login
    if ($_POST) {
        if (auth()->login($_POST['email'], $_POST['password'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'super_admin') {
                header('Location: /super-admin');
            } elseif ($role === 'admin') {
                header('Location: /admin');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            $error = "Credenciales incorrectas";
        }
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - MultiTienda</title>
        <?= $css ?>
    </head>
    <body>
        <div class="auth-container">
            <h1 style="text-align:center;margin-bottom:2rem;color:#667eea;">🏪 MultiTienda</h1>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required value="<?= $_POST['email'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn" style="width:100%;">Iniciar Sesión</button>
                </div>
            </form>
            <div style="text-align:center;margin-top:2rem;font-size:0.9rem;color:#6b7280;">
                <p><strong>Cuentas de prueba:</strong></p>
                <p>Super Admin: admin@multitienda.com / admin123</p>
                <p>Admin Tienda: tienda1@demo.com / demo123</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($path === '/logout') {
    auth()->logout();
}

// Páginas que requieren autenticación
if (strpos($path, '/super-admin') === 0) {
    auth()->requireRole('super_admin');
    
    // Panel Super Admin
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Super Admin - MultiTienda</title>
        <?= $css ?>
    </head>
    <body>
        <div class="container">
            <nav class="navbar">
                <a href="/super-admin" class="logo">👑 Super Admin</a>
                <div class="nav-links">
                    <span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <a href="/super-admin/stores" class="nav-link">Gestionar Tiendas</a>
                    <a href="/super-admin/users" class="nav-link">Usuarios</a>
                    <a href="/super-admin/support" class="nav-link">Soporte</a>
                    <a href="/logout" class="nav-link">Cerrar Sesión</a>
                </div>
            </nav>
            
            <h1 style="color:white;">👑 Panel Super Administrador</h1>
            
            <?php
            // Estadísticas generales
            $stats = [];
            $stats['total_stores'] = storage()->count('stores');
            $stats['total_admins'] = storage()->count('users', 'role', 'admin');
            $stats['total_products'] = storage()->count('products');
            $stats['total_orders'] = storage()->count('orders');
            ?>
            
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_stores'] ?></div>
                    <div class="stat-label">Tiendas Activas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_admins'] ?></div>
                    <div class="stat-label">Administradores</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_products'] ?></div>
                    <div class="stat-label">Productos Totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_orders'] ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>
            </div>
            
            <div class="card">
                <h2>🏬 Tiendas Recientes</h2>
                <?php
                $stores = storage()->load('stores');
                // Simular JOIN con usuarios
                foreach ($stores as &$store) {
                    $admin = storage()->find('users', 'id', $store['admin_id']);
                    $store['admin_name'] = $admin ? $admin['name'] : 'Usuario eliminado';
                }
                unset($store); // Limpiar referencia
                
                // Limitar a 5 más recientes
                $stores = array_slice(array_reverse($stores), 0, 5);
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tienda</th>
                            <th>Administrador</th>
                            <th>Estado</th>
                            <th>Creada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stores as $store): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($store['name']) ?></strong><br>
                                <small><?= htmlspecialchars($store['slug']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($store['admin_name']) ?></td>
                            <td>
                                <span class="badge badge-success"><?= ucfirst($store['status']) ?></span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($store['created_at'])) ?></td>
                            <td>
                                <a href="/store/<?= $store['slug'] ?>" class="btn" style="padding:0.25rem 0.75rem;font-size:0.875rem;" target="_blank">Ver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (strpos($path, '/admin') === 0 && $path !== '/admin@multitienda.com') {
    auth()->requireRole('admin');
    
    // Panel Admin de Tienda
    $user = auth()->getUser();
    $store = storage()->find('stores', 'admin_id', $user['id']);
    
    if (!$store) {
        echo "No tienes una tienda asignada. Contacta al super administrador.";
        exit;
    }
    
    // Manejar formularios POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($path === '/admin/products' && isset($_POST['action'])) {
            if ($_POST['action'] === 'add_product') {
                $new_product = [
                    'store_id' => $store['id'],
                    'name' => $_POST['name'],
                    'slug' => strtolower(str_replace(' ', '-', $_POST['name'])),
                    'description' => $_POST['description'],
                    'price' => floatval($_POST['price']),
                    'stock' => intval($_POST['stock']),
                    'status' => $_POST['status'] ?? 'active',
                    'featured' => isset($_POST['featured'])
                ];
                storage()->insert('products', $new_product);
                $success_message = "Producto agregado exitosamente";
            }
        }
        
        if ($path === '/admin/design' && isset($_POST['action'])) {
            if ($_POST['action'] === 'update_store') {
                $updates = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'theme_color' => $_POST['theme_color']
                ];
                storage()->update('stores', $store['id'], $updates);
                $store = storage()->find('stores', 'id', $store['id']); // Refresh
                $success_message = "Tienda actualizada exitosamente";
            }
        }
    }
    
    // Gestión de Productos
    if ($path === '/admin/products') {
        $products = storage()->findAll('products', 'store_id', $store['id']);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Productos - <?= htmlspecialchars($store['name']) ?></title>
            <?= $css ?>
        </head>
        <body>
            <div class="container">
                <nav class="navbar">
                    <a href="/admin" class="logo">🏬 <?= htmlspecialchars($store['name']) ?></a>
                    <div class="nav-links">
                        <span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="/admin/products" class="nav-link" style="background:rgba(255,255,255,0.2);">Productos</a>
                        <a href="/admin/orders" class="nav-link">Pedidos</a>
                        <a href="/admin/design" class="nav-link">Diseño</a>
                        <a href="/store/<?= $store['slug'] ?>" class="nav-link" target="_blank">Ver Tienda</a>
                        <a href="/logout" class="nav-link">Cerrar Sesión</a>
                    </div>
                </nav>
                
                <h1 style="color:white;">📦 Gestión de Productos</h1>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                
                <!-- Formulario para agregar producto -->
                <div class="card">
                    <h2>➕ Agregar Nuevo Producto</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_product">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="form-group">
                                <label>Nombre del Producto</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Precio ($)</label>
                                <input type="number" step="0.01" name="price" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" name="stock" value="0">
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="status">
                                    <option value="active">Activo</option>
                                    <option value="draft">Borrador</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="featured"> Producto Destacado
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Agregar Producto</button>
                    </form>
                </div>
                
                <!-- Lista de productos -->
                <div class="card">
                    <h2>📋 Mis Productos (<?= count($products) ?>)</h2>
                    <?php if (empty($products)): ?>
                        <p>No tienes productos aún. ¡Agrega tu primer producto arriba!</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                        <?php if ($product['featured'] ?? false): ?>
                                            <span class="badge badge-warning">Destacado</span>
                                        <?php endif; ?>
                                        <br><small><?= htmlspecialchars(substr($product['description'] ?? '', 0, 50)) ?>...</small>
                                    </td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?? 0 ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($product['status'] === 'active') ? 'success' : 'warning' ?>">
                                            <?= ucfirst($product['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn" style="padding:0.25rem 0.75rem;font-size:0.875rem;">Editar</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    // Gestión de Pedidos
    if ($path === '/admin/orders') {
        $orders = storage()->findAll('orders', 'store_id', $store['id']);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pedidos - <?= htmlspecialchars($store['name']) ?></title>
            <?= $css ?>
        </head>
        <body>
            <div class="container">
                <nav class="navbar">
                    <a href="/admin" class="logo">🏬 <?= htmlspecialchars($store['name']) ?></a>
                    <div class="nav-links">
                        <span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="/admin/products" class="nav-link">Productos</a>
                        <a href="/admin/orders" class="nav-link" style="background:rgba(255,255,255,0.2);">Pedidos</a>
                        <a href="/admin/design" class="nav-link">Diseño</a>
                        <a href="/store/<?= $store['slug'] ?>" class="nav-link" target="_blank">Ver Tienda</a>
                        <a href="/logout" class="nav-link">Cerrar Sesión</a>
                    </div>
                </nav>
                
                <h1 style="color:white;">📋 Gestión de Pedidos</h1>
                
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($orders) ?></div>
                        <div class="stat-label">Total Pedidos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= count(array_filter($orders, fn($o) => ($o['status'] ?? '') === 'pending')) ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= count(array_filter($orders, fn($o) => ($o['status'] ?? '') === 'delivered')) ?></div>
                        <div class="stat-label">Entregados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">$<?= number_format(array_sum(array_map(fn($o) => floatval($o['total'] ?? 0), $orders)), 2) ?></div>
                        <div class="stat-label">Ventas Totales</div>
                    </div>
                </div>
                
                <div class="card">
                    <h2>📦 Pedidos Recientes</h2>
                    <?php if (empty($orders)): ?>
                        <div style="text-align:center;padding:3rem;">
                            <h3>📭 No hay pedidos aún</h3>
                            <p>Cuando recibas pedidos aparecerán aquí.</p>
                            <a href="/store/<?= $store['slug'] ?>" target="_blank" class="btn">Ver mi Tienda</a>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($orders) as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($order['customer_name'] ?? 'Cliente') ?></strong><br>
                                        <small><?= htmlspecialchars($order['customer_email'] ?? '') ?></small>
                                    </td>
                                    <td>$<?= number_format($order['total'] ?? 0, 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($order['status'] ?? 'pending') === 'delivered' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($order['status'] ?? 'pending') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($order['created_at'] ?? 'now')) ?></td>
                                    <td>
                                        <button class="btn" style="padding:0.25rem 0.75rem;font-size:0.875rem;">Ver Detalles</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    // Editor de Diseño
    if ($path === '/admin/design') {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Diseño - <?= htmlspecialchars($store['name']) ?></title>
            <?= $css ?>
        </head>
        <body>
            <div class="container">
                <nav class="navbar">
                    <a href="/admin" class="logo">🏬 <?= htmlspecialchars($store['name']) ?></a>
                    <div class="nav-links">
                        <span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="/admin/products" class="nav-link">Productos</a>
                        <a href="/admin/orders" class="nav-link">Pedidos</a>
                        <a href="/admin/design" class="nav-link" style="background:rgba(255,255,255,0.2);">Diseño</a>
                        <a href="/store/<?= $store['slug'] ?>" class="nav-link" target="_blank">Ver Tienda</a>
                        <a href="/logout" class="nav-link">Cerrar Sesión</a>
                    </div>
                </nav>
                
                <h1 style="color:white;">🎨 Editor de Diseño</h1>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <h2>⚙️ Configuración de la Tienda</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_store">
                        <div class="form-group">
                            <label>Nombre de la Tienda</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($store['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="description" rows="3"><?= htmlspecialchars($store['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Color Principal</label>
                            <input type="color" name="theme_color" value="<?= htmlspecialchars($store['theme_color'] ?? '#667eea') ?>">
                            <small>Este color se usará como tema principal de tu tienda</small>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    </form>
                </div>
                
                <div class="card">
                    <h2>🔗 Enlace de tu Tienda</h2>
                    <p>Comparte este enlace para que los clientes puedan comprar en tu tienda:</p>
                    <div style="background:#f8fafc;padding:1rem;border-radius:6px;margin:1rem 0;font-family:monospace;font-size:1.1rem;">
                        <strong>https://<?= $_SERVER['HTTP_HOST'] ?>/store/<?= $store['slug'] ?></strong>
                    </div>
                    <a href="/store/<?= $store['slug'] ?>" class="btn" target="_blank">Ver mi Tienda</a>
                </div>
                
                <div class="card">
                    <h2>📊 Vista Previa</h2>
                    <p>Así se ve tu tienda actualmente:</p>
                    <iframe src="/store/<?= $store['slug'] ?>" style="width:100%;height:400px;border:1px solid #e5e7eb;border-radius:8px;"></iframe>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - <?= htmlspecialchars($store['name']) ?></title>
        <?= $css ?>
    </head>
    <body>
        <div class="container">
            <nav class="navbar">
                <a href="/admin" class="logo">🏬 <?= htmlspecialchars($store['name']) ?></a>
                <div class="nav-links">
                    <span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <a href="/admin/products" class="nav-link">Productos</a>
                    <a href="/admin/orders" class="nav-link">Pedidos</a>
                    <a href="/admin/design" class="nav-link">Diseño</a>
                    <a href="/store/<?= $store['slug'] ?>" class="nav-link" target="_blank">Ver Tienda</a>
                    <a href="/logout" class="nav-link">Cerrar Sesión</a>
                </div>
            </nav>
            
            <h1 style="color:white;">🏬 Panel de Administración - <?= htmlspecialchars($store['name']) ?></h1>
            
            <?php
            // Estadísticas de la tienda
            $store_stats = [];
            $store_stats['products'] = storage()->count('products', 'store_id', $store['id']);
            $store_stats['orders'] = storage()->count('orders', 'store_id', $store['id']);
            
            // Calcular ingresos
            $orders = storage()->findAll('orders', 'store_id', $store['id']);
            $store_stats['revenue'] = 0;
            foreach ($orders as $order) {
                if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
                    $store_stats['revenue'] += floatval($order['total'] ?? 0);
                }
            }
            ?>
            
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $store_stats['products'] ?></div>
                    <div class="stat-label">Productos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $store_stats['orders'] ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">$<?= number_format($store_stats['revenue'], 2) ?></div>
                    <div class="stat-label">Ingresos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= ucfirst($store['status']) ?></div>
                    <div class="stat-label">Estado</div>
                </div>
            </div>
            
            <div class="card">
                <h2>🔗 Enlace de tu Tienda</h2>
                <p>Comparte este enlace para que los clientes puedan comprar en tu tienda:</p>
                <div style="background:#f8fafc;padding:1rem;border-radius:6px;margin:1rem 0;font-family:monospace;font-size:1.1rem;">
                    <strong>https://<?= $_SERVER['HTTP_HOST'] ?>/store/<?= $store['slug'] ?></strong>
                </div>
                <a href="/store/<?= $store['slug'] ?>" class="btn" target="_blank">Ver mi Tienda</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Página principal (home)
if ($path === '' || $path === '/') {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MultiTienda - Plataforma E-commerce</title>
        <?= $css ?>
    </head>
    <body>
        <div class="container">
            <div class="hero">
                <h1>🏪 MultiTienda</h1>
                <p>Plataforma completa para gestionar múltiples tiendas online</p>
                
                <?php if (auth()->isLoggedIn()): ?>
                    <div style="margin-top:2rem;">
                        <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
                        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                            <a href="/super-admin" class="btn">Ir al Panel Super Admin</a>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="/admin" class="btn">Ir a mi Panel de Tienda</a>
                        <?php endif; ?>
                        <a href="/logout" class="btn btn-danger" style="margin-left:1rem;">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>🏬 Tiendas Disponibles</h2>
                <?php
                $public_stores = storage()->findAll('stores', 'status', 'active');
                // Limitar a 6
                $public_stores = array_slice($public_stores, 0, 6);
                ?>
                
                <?php if (empty($public_stores)): ?>
                    <p>No hay tiendas disponibles aún.</p>
                <?php else: ?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;margin-top:2rem;">
                        <?php foreach ($public_stores as $store): ?>
                        <div style="border:1px solid #e5e7eb;border-radius:8px;padding:1.5rem;text-align:center;">
                            <h3><?= htmlspecialchars($store['name']) ?></h3>
                            <p style="color:#6b7280;margin:1rem 0;"><?= htmlspecialchars($store['description'] ?: 'Tienda online') ?></p>
                            <a href="/store/<?= $store['slug'] ?>" class="btn">Visitar Tienda</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Vista pública de tienda individual
if (preg_match('/^\/store\/([a-zA-Z0-9-]+)$/', $path, $matches)) {
    $store_slug = $matches[1];
    
    $store = storage()->find('stores', 'slug', $store_slug);
    
    if (!$store || $store['status'] !== 'active') {
        echo "Tienda no encontrada";
        exit;
    }
    
    $products = storage()->findAll('products', 'store_id', $store['id']);
    // Filtrar solo productos activos y ordenar
    $products = array_filter($products, function($p) {
        return isset($p['status']) && $p['status'] === 'active';
    });
    
    // Ordenar por featured primero
    usort($products, function($a, $b) {
        $aFeatured = isset($a['featured']) && $a['featured'];
        $bFeatured = isset($b['featured']) && $b['featured'];
        if ($aFeatured && !$bFeatured) return -1;
        if (!$aFeatured && $bFeatured) return 1;
        return 0;
    });
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($store['name']) ?> - Tienda Online</title>
        <?= $css ?>
        <style>
        body { background: <?= htmlspecialchars($store['theme_color'] ?? '#667eea') ?>; }
        .store-header { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 2rem; margin-bottom: 2rem; border-radius: 12px; text-align: center; color: white; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .product-card:hover { transform: translateY(-4px); }
        .product-image { width: 100%; height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .product-info { padding: 1.5rem; }
        .product-price { font-size: 1.5rem; font-weight: bold; color: #059669; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="store-header">
                <h1>🏬 <?= htmlspecialchars($store['name']) ?></h1>
                <p><?= htmlspecialchars($store['description'] ?: 'Bienvenido a nuestra tienda online') ?></p>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="card" style="text-align:center;">
                    <h2>🔧 Tienda en Construcción</h2>
                    <p>Esta tienda está siendo configurada. Pronto tendremos productos disponibles.</p>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2>🛍️ Nuestros Productos</h2>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">📦</div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p style="color:#6b7280;margin:0.5rem 0;"><?= htmlspecialchars(substr($product['description'] ?: '', 0, 100)) ?>...</p>
                                <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                <button class="btn" style="width:100%;margin-top:1rem;">Agregar al Carrito</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 404
echo "<!DOCTYPE html><html><head><title>404</title></head><body>";
echo "<div style='text-align:center;margin:50px;font-family:system-ui;'>";
echo "<h1>404 - Página no encontrada</h1>";
echo "<a href='/' style='background:#667eea;color:white;padding:1rem 2rem;border-radius:8px;text-decoration:none;'>Volver al inicio</a>";
echo "</div></body></html>";
?>
