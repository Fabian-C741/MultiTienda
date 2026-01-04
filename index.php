<?php
/**
 * MultiTienda - Diagnóstico de Error 500
 */

// Habilitar reporte de errores completo
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔍 Diagnóstico Error 500 - MultiTienda</h1>";

try {
    // 1. Verificar PHP
    echo "<p>✓ PHP Version: " . phpversion() . "</p>";
    
    // 2. Verificar directorio backend
    $backendPath = __DIR__ . '/backend';
    echo "<p>✓ Backend Path: " . $backendPath . "</p>";
    
    if (!is_dir($backendPath)) {
        throw new Exception('Backend directory not found');
    }
    echo "<p>✓ Backend directory exists</p>";
    
    // 3. Verificar vendor
    $vendorPath = $backendPath . '/vendor/autoload.php';
    if (!file_exists($vendorPath)) {
        throw new Exception('Vendor autoload not found: ' . $vendorPath);
    }
    echo "<p>✓ Vendor autoload found</p>";
    
    // 4. Intentar cargar autoload
    require $vendorPath;
    echo "<p>✓ Autoload cargado</p>";
    
    // 5. Verificar bootstrap
    $bootstrapPath = $backendPath . '/bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        throw new Exception('Bootstrap not found: ' . $bootstrapPath);
    }
    echo "<p>✓ Bootstrap found</p>";
    
    // 6. Verificar .env
    $envPath = $backendPath . '/.env';
    if (!file_exists($envPath)) {
        echo "<p>⚠️ Warning: .env file not found</p>";
    } else {
        echo "<p>✓ .env file found</p>";
    }
    
    echo "<p>🚀 Intentando cargar Laravel...</p>";
    
    // 7. Cargar Laravel con captura de errores
    define('LARAVEL_START', microtime(true));
    
    use Illuminate\Http\Request;
    
    $app = require_once $bootstrapPath;
    echo "<p>✓ Laravel app creada</p>";
    
    $response = $app->handleRequest(Request::capture());
    echo "<p>✓ Request procesado, enviando respuesta...</p>";
    
    $response->send();
    
} catch (Throwable $e) {
    echo '<div style="background: #ffebee; border: 1px solid #f44336; border-radius: 5px; padding: 20px; margin: 20px 0;">';
    echo '<h2 style="color: #d32f2f;">❌ Error Detectado:</h2>';
    echo '<p><strong>Tipo:</strong> ' . get_class($e) . '</p>';
    echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
    echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
    echo '<h3>Stack Trace:</h3>';
    echo '<pre style="background: #f5f5f5; padding: 10px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
