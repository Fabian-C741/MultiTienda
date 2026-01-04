<?php
// Script para crear tablas en producción
echo "<h1>🔧 Configurando base de datos</h1>";

try {
    // 1. Cargar Laravel
    require_once __DIR__ . '/backend/vendor/autoload.php';
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    
    echo "<p>✓ Laravel cargado</p>";
    
    // 2. Crear archivo SQLite si no existe
    $dbPath = __DIR__ . '/backend/database/database.sqlite';
    if (!file_exists($dbPath)) {
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }
        touch($dbPath);
        echo "<p>✓ Base de datos SQLite creada</p>";
    }
    
    // 3. Actualizar .env para usar archivo SQLite
    $envContent = 'APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:bJ+rtI0X835KUSr9ekJQbJAnOEMH3tXkBR8GN8ea+fM=
APP_DEBUG=true
APP_URL=https://multitienda.kcrsf.com
LOG_CHANNEL=single
DB_CONNECTION=sqlite
DB_DATABASE=' . $dbPath . '
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
TENANCY_ENABLED=true
';
    
    file_put_contents(__DIR__ . '/backend/.env', $envContent);
    echo "<p>✓ .env actualizado con BD SQLite</p>";
    
    echo "<p>🎉 <strong>Configuración completada!</strong></p>";
    echo "<p>Ahora ve a <a href='/'>MultiTienda</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>