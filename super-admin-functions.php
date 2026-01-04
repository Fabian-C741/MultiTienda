<?php
/**
 * 🏪 MultiTienda Pro - Funciones Avanzadas de Super Admin
 * Complemento profesional para el panel de administración
 */

function renderSuperAdminCreateForm() {
    return '
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-user-plus"></i>
                Crear Nuevo Administrador Principal
            </h2>
        </div>
        <div class="card-content">
            <form method="POST" style="max-width: 600px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Nombre Completo
                        </label>
                        <input type="text" name="name" class="form-input" required placeholder="Juan Pérez">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input type="email" name="email" class="form-input" required placeholder="admin@tienda.com">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input type="password" name="password" class="form-input" required placeholder="••••••••" minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Teléfono (Opcional)
                        </label>
                        <input type="tel" name="phone" class="form-input" placeholder="+1 234 567 8900">
                    </div>
                </div>
                
                <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Importante:</strong> Este administrador podrá crear y gestionar su propia tienda, así como ver estadísticas detalladas y personalizar el diseño de su tienda.
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Administrador
                    </button>
                    <a href="/super-admin" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>';
}

function renderStoresList($stores) {
    if (empty($stores)) {
        return '
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-store-alt"></i>
                    Tiendas Registradas
                </h2>
            </div>
            <div class="card-content">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <h3 class="empty-state-title">No hay tiendas registradas</h3>
                    <p class="empty-state-description">Las tiendas aparecerán aquí cuando los administradores las creen</p>
                </div>
            </div>
        </div>';
    }
    
    $content = '
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-store-alt"></i>
                Todas las Tiendas ('.count($stores).')
            </h2>
        </div>
        <div class="card-content">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tienda</th>
                            <th>Administrador</th>
                            <th>Productos</th>
                            <th>Pedidos</th>
                            <th>Ventas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    foreach ($stores as $store) {
        $admin = storage()->findOne('users', ['id' => $store['admin_id']]);
        $products = storage()->find('products', ['store_id' => $store['id']]);
        $orders = storage()->find('orders', ['store_id' => $store['id']]);
        
        $content .= '<tr>
            <td>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                        '.strtoupper(substr($store['name'], 0, 2)).'
                    </div>
                    <div>
                        <div style="font-weight: 600;">'.$store['name'].'</div>
                        <div style="font-size: 0.875rem; color: var(--gray-500);">'.$store['slug'].'</div>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <div style="font-weight: 500;">'.($admin['name'] ?? 'Sin asignar').'</div>
                    <div style="font-size: 0.875rem; color: var(--gray-500);">'.($admin['email'] ?? '').'</div>
                </div>
            </td>
            <td><span class="badge badge-info">'.count($products).'</span></td>
            <td><span class="badge badge-success">'.count($orders).'</span></td>
            <td><strong>$'.number_format($store['total_sales'] ?? 0, 0).'</strong></td>
            <td><span class="badge badge-success">Activa</span></td>
            <td>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/tienda/'.$store['slug'].'" target="_blank" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem;">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <button class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem;" onclick="alert(\'Funcionalidad en desarrollo\')">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </td>
        </tr>';
    }
    
    $content .= '
                    </tbody>
                </table>
            </div>
        </div>
    </div>';
    
    return $content;
}

function renderAnalyticsDashboard($stores, $admins) {
    $totalProducts = 0;
    $totalOrders = 0;
    $totalSales = 0;
    
    foreach ($stores as $store) {
        $products = storage()->find('products', ['store_id' => $store['id']]);
        $orders = storage()->find('orders', ['store_id' => $store['id']]);
        
        $totalProducts += count($products);
        $totalOrders += count($orders);
        $totalSales += ($store['total_sales'] ?? 0);
    }
    
    return '
    <div class="dashboard-grid">
        <div class="metric-card">
            <div class="metric-icon primary">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="metric-value" id="metric-stores">'.count($stores).'</div>
            <div class="metric-label">Tiendas Totales</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i>
                +18% crecimiento
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon success">
                <i class="fas fa-box"></i>
            </div>
            <div class="metric-value" id="metric-products">'.$totalProducts.'</div>
            <div class="metric-label">Productos Publicados</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i>
                +25% este mes
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon info">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="metric-value" id="metric-orders">'.$totalOrders.'</div>
            <div class="metric-label">Pedidos Procesados</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i>
                +32% este mes
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon warning">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="metric-value" id="metric-sales">$'.number_format($totalSales, 0).'</div>
            <div class="metric-label">Ingresos Totales</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i>
                +28% este mes
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Tendencia de Ventas
                </h3>
            </div>
            <div class="card-content">
                <div style="height: 300px; position: relative;">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Productos por Categoría
                </h3>
            </div>
            <div class="card-content">
                <div style="height: 300px; position: relative;">
                    <canvas id="categoryChart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Rendimiento por Tienda
                </h3>
            </div>
            <div class="card-content">
                <div style="height: 300px; position: relative;">
                    <canvas id="storeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Actividad de Usuarios
                </h3>
            </div>
            <div class="card-content">
                <div style="height: 300px; position: relative;">
                    <canvas id="activityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy"></i>
                    Top Tiendas por Rendimiento
                </h3>
            </div>
            <div class="card-content">
                <div style="display: flex; flex-direction: column; gap: 1rem;">';
    
    // Ordenar tiendas por ventas
    usort($stores, function($a, $b) {
        return ($b['total_sales'] ?? 0) <=> ($a['total_sales'] ?? 0);
    });
    
    foreach (array_slice($stores, 0, 5) as $i => $store) {
        $badgeColors = ['primary', 'success', 'warning', 'info', 'secondary'];
        $badgeColor = $badgeColors[$i] ?? 'secondary';
        
        $content .= '
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; background: linear-gradient(135deg, var(--neutral-50) 0%, var(--primary-50) 100%); border-radius: var(--radius-xl); border: 1px solid var(--neutral-200); transition: all 0.3s; cursor: pointer;" 
             onmouseover="this.style.transform=\'scale(1.02)\'" 
             onmouseout="this.style.transform=\'scale(1)\'">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 3rem; height: 3rem; background: linear-gradient(135deg, var(--'.$badgeColor.'-600) 0%, var(--'.$badgeColor.'-700) 100%); color: white; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.125rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                    #'.($i + 1).'
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 1.125rem; color: var(--neutral-900);">'.$store['name'].'</div>
                    <div style="font-size: 0.875rem; color: var(--neutral-500); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-link"></i>
                        /tienda/'.$store['slug'].'
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: var(--success-600); font-size: 1.25rem;">$'.number_format($store['total_sales'] ?? 0, 0).'</div>
                <div style="font-size: 0.875rem; color: var(--neutral-500);">en ventas totales</div>
            </div>
        </div>';
    }
    
    $content .= '
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users-cog"></i>
                    Administradores del Sistema
                </h3>
            </div>
            <div class="card-content">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    '.(!empty($admins) ? implode('', array_map(function($admin) {
                        $store = storage()->findOne('stores', ['admin_id' => $admin['id']]);
                        $userInitial = strtoupper(substr($admin['name'], 0, 1));
                        
                        return '
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; background: linear-gradient(135deg, var(--neutral-50) 0%, var(--secondary-50) 100%); border-radius: var(--radius-xl); border: 1px solid var(--neutral-200); transition: all 0.3s;" 
                             onmouseover="this.style.transform=\'translateY(-4px)\'; this.style.boxShadow=\'var(--shadow-lg)\'" 
                             onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'var(--shadow)\'">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 3rem; height: 3rem; background: linear-gradient(135deg, var(--secondary-600) 0%, var(--info-600) 100%); color: white; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-weight: 700; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                                    '.$userInitial.'
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1.125rem; color: var(--neutral-900);">'.$admin['name'].'</div>
                                    <div style="font-size: 0.875rem; color: var(--neutral-500);">'.$admin['email'].'</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge '.($store ? 'badge-success' : 'badge-warning').'" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                    <i class="fas '.($store ? 'fa-check-circle' : 'fa-exclamation-triangle').'"></i>
                                    '.($store ? 'Con tienda' : 'Sin tienda').'
                                </span>
                                '.($store ? '<div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">'.$store['name'].'</div>' : '').'
                            </div>
                        </div>';
                    }, $admins)) : '
                    <div class="empty-state" style="padding: 2rem;">
                        <div class="empty-state-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h4 class="empty-state-title">No hay administradores</h4>
                        <p class="empty-state-description">Los administradores aparecerán aquí cuando se registren</p>
                        <a href="/super-admin/create-admin" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Administrador
                        </a>
                    </div>').'
                </div>
            </div>
        </div>
    </div>';
    
    return $content;
}
?>