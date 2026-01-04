<?php
// Diagnóstico directo - evitando .htaccess
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 DIAGNÓSTICO DIRECTO</h1>";

try {
    echo "<p>1. Cargando autoload...</p>";
    require_once __DIR__ . '/backend/vendor/autoload.php';
    echo "<p>✓ Autoload OK</p>";
    
    echo "<p>2. Verificando .env...</p>";
    $envPath = __DIR__ . '/backend/.env';
    if (!file_exists($envPath)) {
        echo "<p>⚠️ Creando .env...</p>";
        $envContent = "APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh
APP_DEBUG=true
APP_URL=https://multitienda.kcrsf.com
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
LOG_CHANNEL=single";
        file_put_contents($envPath, $envContent);
        echo "<p>✓ .env creado</p>";
    }
    
    echo "<p>3. Verificando storage permissions...</p>";
    $storagePath = __DIR__ . '/backend/storage';
    if (!is_writable($storagePath)) {
        echo "<p>❌ Storage no escribible</p>";
    } else {
        echo "<p>✓ Storage OK</p>";
    }
    
    // Crear directorio de logs si no existe
    $logsPath = __DIR__ . '/backend/storage/logs';
    if (!is_dir($logsPath)) {
        mkdir($logsPath, 0755, true);
        echo "<p>✓ Directorio logs creado</p>";
    }
    
    echo "<p>4. Iniciando Laravel...</p>";
    define('LARAVEL_START', microtime(true));
    
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    echo "<p>✓ Laravel bootstrap OK</p>";
    
    echo "<p>🎉 Laravel carga correctamente - problema es en las rutas</p>";
    
} catch (Exception $e) {
    echo "<div style='background:#ffcdd2;padding:15px;margin:10px;border-radius:5px;'>";
    echo "<h3>❌ ERROR: " . get_class($e) . "</h3>";
    echo "<p><b>Mensaje:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>Archivo:</b> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}
?>