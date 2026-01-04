<?php
// Forzar mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 0);

echo "<h1>DEBUG FINAL</h1>";

try {
    use Illuminate\Http\Request;
    
    define('LARAVEL_START', microtime(true));
    
    $backendPath = __DIR__ . '/backend';
    
    // Autoloader
    require $backendPath . '/vendor/autoload.php';
    echo "✓ Autoload OK<br>";
    
    // Crear .env básico
    $envPath = $backendPath . '/.env';
    if (!file_exists($envPath)) {
        $envContent = "APP_NAME=MultiTienda\nAPP_ENV=production\nAPP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh\nAPP_DEBUG=true\nAPP_URL=https://multitienda.kcrsf.com\nDB_CONNECTION=sqlite\nDB_DATABASE=:memory:\nLOG_CHANNEL=single\n";
        file_put_contents($envPath, $envContent);
        echo "✓ .env creado<br>";
    } else {
        echo "✓ .env existe<br>";
    }
    
    // Bootstrap Laravel
    $app = require_once $backendPath . '/bootstrap/app.php';
    echo "✓ Laravel cargado<br>";
    
    // Capturar request
    $request = Request::capture();
    echo "✓ Request: " . $request->getPathInfo() . "<br>";
    
    // AQUÍ ES DONDE PROBABLEMENTE FALLA
    echo "🚀 Procesando...<br>";
    
    // Intentar procesar
    $response = $app->handleRequest($request);
    echo "✓ Procesado, enviando...<br>";
    
    // Enviar respuesta
    $response->send();
    
} catch (Throwable $e) {
    echo "<div style='background:yellow;padding:20px;'>";
    echo "<h2>❌ ERROR ENCONTRADO</h2>";
    echo "<strong>Tipo:</strong> " . get_class($e) . "<br>";
    echo "<strong>Mensaje:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<strong>Trace:</strong><br>";
    $trace = explode("\n", $e->getTraceAsString());
    for($i = 0; $i < min(5, count($trace)); $i++) {
        echo htmlspecialchars($trace[$i]) . "<br>";
    }
    echo "</div>";
}
?>
