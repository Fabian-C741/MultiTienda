<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNÓSTICO LARAVEL COMPLETO ===<br>";

try {
    echo "1. Cargando autoload...<br>";
    require_once __DIR__ . '/backend/vendor/autoload.php';
    echo "✓ Autoload OK<br>";
    
    echo "2. Definiendo LARAVEL_START...<br>";
    define('LARAVEL_START', microtime(true));
    echo "✓ LARAVEL_START OK<br>";
    
    echo "3. Verificando .env...<br>";
    $envPath = __DIR__ . '/backend/.env';
    if (!file_exists($envPath)) {
        echo "⚠️ Creando .env básico...<br>";
        $envContent = "APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=https://multitienda.kcrsf.com

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

LOG_CHANNEL=single
";
        file_put_contents($envPath, $envContent);
        echo "✓ .env creado<br>";
    } else {
        echo "✓ .env existe<br>";
    }
    
    echo "4. Cargando bootstrap Laravel...<br>";
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    echo "✓ App creada: " . get_class($app) . "<br>";
    
    echo "5. Preparando Request...<br>";
    use Illuminate\Http\Request;
    $request = Request::capture();
    echo "✓ Request: " . $request->getMethod() . " " . $request->getUri() . "<br>";
    
    echo "6. Procesando request con Laravel...<br>";
    $response = $app->handleRequest($request);
    echo "✓ Response: " . get_class($response) . "<br>";
    
    echo "7. Enviando respuesta...<br>";
    $response->send();
    
} catch (Exception $e) {
    echo "<div style='background:#ffcdd2;padding:20px;margin:10px;border:1px solid #f44336;'>";
    echo "<h3>❌ EXCEPTION: " . get_class($e) . "</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    
    // Mostrar solo las primeras líneas del stack trace
    $trace = explode("\n", $e->getTraceAsString());
    echo "<p><strong>Stack Trace:</strong></p><pre>";
    for($i = 0; $i < min(10, count($trace)); $i++) {
        echo htmlspecialchars($trace[$i]) . "\n";
    }
    if(count($trace) > 10) echo "... (más líneas)\n";
    echo "</pre></div>";
    
} catch (Error $e) {
    echo "<div style='background:#ffebee;padding:20px;margin:10px;border:1px solid #d32f2f;'>";
    echo "<h3>💥 FATAL ERROR: " . get_class($e) . "</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}
?>
