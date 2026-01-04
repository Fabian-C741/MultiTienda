<?php
// Diagnóstico completo paso a paso
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 DIAGNÓSTICO COMPLETO PASO A PASO</h1>";

try {
    echo "<p>1. ✓ PHP " . phpversion() . " funcionando</p>";
    
    echo "<p>2. Cargando autoload...</p>";
    require_once __DIR__ . '/backend/vendor/autoload.php';
    echo "<p>✓ Autoload cargado</p>";
    
    echo "<p>3. Definiendo LARAVEL_START...</p>";
    define('LARAVEL_START', microtime(true));
    echo "<p>✓ LARAVEL_START definido</p>";
    
    echo "<p>4. Verificando/creando .env...</p>";
    $envPath = __DIR__ . '/backend/.env';
    if (!file_exists($envPath)) {
        $envContent = "APP_NAME=MultiTienda
APP_ENV=production  
APP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh
APP_DEBUG=true
APP_URL=https://multitienda.kcrsf.com
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
LOG_CHANNEL=single
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
";
        file_put_contents($envPath, $envContent);
        echo "<p>✓ .env creado</p>";
    } else {
        echo "<p>✓ .env existe</p>";
    }
    
    echo "<p>5. Verificando directorios...</p>";
    $dirs = ['storage/logs', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views'];
    foreach($dirs as $dir) {
        $fullPath = __DIR__ . '/backend/' . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
            echo "<p>✓ Creado: $dir</p>";
        }
    }
    
    echo "<p>6. Importando Request...</p>";
    use Illuminate\Http\Request;
    echo "<p>✓ Request importado</p>";
    
    echo "<p>7. Cargando Laravel bootstrap...</p>";
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    echo "<p>✓ Laravel app: " . get_class($app) . "</p>";
    
    echo "<p>8. Creando request...</p>";
    $request = Request::capture();
    echo "<p>✓ Request creado: " . $request->getMethod() . " " . $request->getPathInfo() . "</p>";
    
    echo "<p>9. <strong>PROCESANDO REQUEST</strong> (aquí puede fallar)...</p>";
    $response = $app->handleRequest($request);
    echo "<p>✓ Request procesado: " . get_class($response) . "</p>";
    
    echo "<p>10. Enviando respuesta...</p>";
    $response->send();
    echo "<p>✓ ÉXITO TOTAL</p>";
    
} catch (Exception $e) {
    echo "<div style='background:#ffcdd2;padding:20px;margin:10px;border:2px solid red;'>";
    echo "<h2>🚨 EXCEPTION CAPTURADA</h2>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    $trace = explode("\n", $e->getTraceAsString());
    echo "<pre>";
    for($i = 0; $i < min(8, count($trace)); $i++) {
        echo htmlspecialchars($trace[$i]) . "\n";
    }
    echo "</pre>";
    echo "</div>";
    
} catch (Error $e) {
    echo "<div style='background:#ffebee;padding:20px;margin:10px;border:2px solid darkred;'>";
    echo "<h2>💥 FATAL ERROR</h2>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

echo "<hr><p><em>Diagnóstico completado a las " . date('Y-m-d H:i:s') . "</em></p>";
?>