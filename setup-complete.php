<?php
// Script para ejecutar migraciones y preparar sistema completo
echo "<h1>🔧 Preparando MultiTienda</h1>";

try {
    // Cargar Laravel
    require_once __DIR__ . '/backend/vendor/autoload.php';
    
    // Configurar entorno
    define('LARAVEL_START', microtime(true));
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    
    echo "<p>✅ Laravel cargado correctamente</p>";
    
    // Configurar .env para producción
    $envContent = 'APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:bJ+rtI0X835KUSr9ekJQbJAnOEMH3tXkBR8GN9ea+fM=
APP_DEBUG=true
APP_URL=https://multitienda.kcrsf.com
LOG_CHANNEL=single
DB_CONNECTION=sqlite
DB_DATABASE=/home/u464516792/domains/multitienda.kcrsf.com/public_html/backend/database/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
TENANCY_ENABLED=true
';
    
    file_put_contents(__DIR__ . '/backend/.env', $envContent);
    echo "<p>✅ .env configurado correctamente</p>";
    
    // Crear directorio de base de datos
    $dbDir = __DIR__ . '/backend/database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    // Crear archivo SQLite
    $dbPath = $dbDir . '/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        chmod($dbPath, 0666);
        echo "<p>✅ Base de datos SQLite creada</p>";
    } else {
        echo "<p>✅ Base de datos SQLite ya existe</p>";
    }
    
    // Crear directorios necesarios
    $directories = [
        'backend/storage/logs',
        'backend/storage/framework/cache',
        'backend/storage/framework/sessions', 
        'backend/storage/framework/views',
        'backend/storage/app/public',
        'backend/bootstrap/cache'
    ];
    
    foreach ($directories as $dir) {
        $fullPath = __DIR__ . '/' . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
            echo "<p>✅ Creado directorio: $dir</p>";
        }
    }
    
    echo "<h3>🎉 Sistema preparado exitosamente</h3>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Ve a <a href='/'>MultiTienda Home</a> - Página principal</li>";
    echo "<li>Usa <a href='/central'>Panel Central</a> - Dashboard administrativo</li>";
    echo "<li>Gestiona tiendas en <a href='/central/tenants'>Gestionar Tiendas</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='background:#ffcdd2;padding:20px;margin:10px;'>";
    echo "<h3>❌ Error durante la configuración</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}
?>