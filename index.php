<?php
echo "=== DIAGNÓSTICO LARAVEL PASO A PASO ===<br>";

try {
    // 1. Cargar autoload
    require_once __DIR__ . '/backend/vendor/autoload.php';
    echo "✓ Autoload cargado<br>";
    
    // 2. Definir LARAVEL_START
    define('LARAVEL_START', microtime(true));
    echo "✓ LARAVEL_START definido<br>";
    
    // 3. Verificar bootstrap
    $bootstrapPath = __DIR__ . '/backend/bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        throw new Exception("Bootstrap no encontrado: $bootstrapPath");
    }
    echo "✓ Bootstrap existe<br>";
    
    // 4. Cargar bootstrap
    $app = require_once $bootstrapPath;
    echo "✓ Bootstrap cargado, tipo: " . get_class($app) . "<br>";
    
    // 5. Verificar .env
    $envPath = __DIR__ . '/backend/.env';
    if (!file_exists($envPath)) {
        echo "⚠️ .env no existe - creando básico<br>";
        // Crear .env básico
        $envContent = "APP_NAME=MultiTienda\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=https://multitienda.kcrsf.com\n\nDB_CONNECTION=sqlite\nDB_DATABASE=:memory:\n";
        file_put_contents($envPath, $envContent);
        echo "✓ .env básico creado<br>";
    } else {
        echo "✓ .env existe<br>";
    }
    
    // 6. Importar Request
    use Illuminate\Http\Request;
    echo "✓ Request importado<br>";
    
    // 7. Crear Request
    $request = Request::capture();
    echo "✓ Request capturado: " . $request->getMethod() . " " . $request->getUri() . "<br>";
    
    // 8. Handle request
    echo "🚀 Procesando request...<br>";
    $response = $app->handleRequest($request);
    echo "✓ Request procesado, tipo respuesta: " . get_class($response) . "<br>";
    
    // 9. Enviar respuesta
    $response->send();
    echo "✓ Respuesta enviada<br>";
    
} catch (Exception $e) {
    echo "<div style='background:#ffebee;padding:20px;margin:10px;border-radius:5px;'>";
    echo "<strong>❌ ERROR:</strong> " . get_class($e) . "<br>";
    echo "<strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";  
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "<strong>Trace:</strong><br><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
} catch (Error $e) {
    echo "<div style='background:#ffcdd2;padding:20px;margin:10px;border-radius:5px;'>";
    echo "<strong>❌ FATAL ERROR:</strong> " . get_class($e) . "<br>";
    echo "<strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "<strong>Trace:</strong><br><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
?>
