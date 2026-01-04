<?php
/**
 * 🗄️ MultiTienda Database Setup
 * Crea todas las tablas necesarias para el sistema completo
 */

// Configuración de base de datos
$db_host = 'localhost';
$db_name = 'multitienda';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🗄️ Configurando Base de Datos MultiTienda</h1>";
    
    // 1. Tabla de usuarios (Super Admins, Admins, Clientes)
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin', 'customer') DEFAULT 'customer',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        avatar VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p>✅ Tabla 'users' creada</p>";
    
    // 2. Tabla de tiendas (cada admin tiene su tienda)
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        domain VARCHAR(255) UNIQUE NULL,
        description TEXT NULL,
        logo VARCHAR(255) NULL,
        theme_color VARCHAR(7) DEFAULT '#667eea',
        custom_css TEXT NULL,
        status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
        settings JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p>✅ Tabla 'stores' creada</p>";
    
    // 3. Tabla de productos
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description TEXT NULL,
        price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2) NULL,
        stock INT DEFAULT 0,
        sku VARCHAR(255) NULL,
        images JSON NULL,
        status ENUM('active', 'inactive', 'draft') DEFAULT 'draft',
        featured BOOLEAN DEFAULT FALSE,
        seo_title VARCHAR(255) NULL,
        seo_description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
        UNIQUE KEY unique_store_slug (store_id, slug)
    )");
    echo "<p>✅ Tabla 'products' creada</p>";
    
    // 4. Tabla de categorías
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        parent_id INT NULL,
        description TEXT NULL,
        image VARCHAR(255) NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
        UNIQUE KEY unique_store_slug (store_id, slug)
    )");
    echo "<p>✅ Tabla 'categories' creada</p>";
    
    // 5. Tabla de pedidos
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        customer_id INT NULL,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50) NULL,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        shipping_address JSON NOT NULL,
        billing_address JSON NULL,
        payment_method VARCHAR(50) NULL,
        payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p>✅ Tabla 'orders' creada</p>";
    
    // 6. Tabla de items del pedido
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    echo "<p>✅ Tabla 'order_items' creada</p>";
    
    // 7. Tabla de páginas personalizadas
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        content LONGTEXT NULL,
        template VARCHAR(50) DEFAULT 'default',
        status ENUM('published', 'draft') DEFAULT 'draft',
        seo_title VARCHAR(255) NULL,
        seo_description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
        UNIQUE KEY unique_store_slug (store_id, slug)
    )");
    echo "<p>✅ Tabla 'pages' creada</p>";
    
    // 8. Tabla de tickets de soporte
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        store_id INT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        assigned_to INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p>✅ Tabla 'support_tickets' creada</p>";
    
    // Insertar Super Admin por defecto
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("
    INSERT IGNORE INTO users (email, password, name, role) 
    VALUES ('admin@multitienda.com', '$admin_password', 'Super Administrator', 'super_admin')
    ");
    echo "<p>✅ Super Admin creado (admin@multitienda.com / admin123)</p>";
    
    // Insertar datos de ejemplo
    $demo_admin_password = password_hash('demo123', PASSWORD_DEFAULT);
    $pdo->exec("
    INSERT IGNORE INTO users (email, password, name, role) 
    VALUES ('tienda1@demo.com', '$demo_admin_password', 'Admin Tienda 1', 'admin')
    ");
    
    $pdo->exec("
    INSERT IGNORE INTO stores (admin_id, name, slug, description) 
    VALUES (2, 'Tienda Electrónica', 'electronica', 'La mejor tienda de electrónicos')
    ");
    
    echo "<p>✅ Datos de ejemplo creados</p>";
    
    echo "<hr>";
    echo "<h2>🎉 Base de Datos Configurada Correctamente</h2>";
    echo "<p><strong>Credenciales de acceso:</strong></p>";
    echo "<ul>";
    echo "<li>Super Admin: admin@multitienda.com / admin123</li>";
    echo "<li>Demo Admin: tienda1@demo.com / demo123</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<div style='background:#ffcdd2;padding:20px;margin:10px;border-radius:8px;'>";
    echo "<h3>❌ Error de Base de Datos</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Solución:</strong> Verifica que MySQL esté funcionando y que la base de datos 'multitienda' existe.</p>";
    echo "</div>";
}
?>