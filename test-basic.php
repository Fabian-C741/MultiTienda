<?php
echo "=== TEST BÁSICO FUNCIONANDO ===<br>";
echo "PHP: " . phpversion() . "<br>";

// Test 1: Autoload
echo "<br>TEST 1: Autoload<br>";
try {
    require_once __DIR__ . '/backend/vendor/autoload.php';
    echo "✓ Autoload OK<br>";
} catch (Throwable $e) {
    echo "❌ Autoload FALLA: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: .env básico
echo "<br>TEST 2: .env<br>";
$envPath = __DIR__ . '/backend/.env';
if (!file_exists($envPath)) {
    file_put_contents($envPath, "APP_NAME=Test\nAPP_ENV=local\nAPP_DEBUG=true\nAPP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh\nDB_CONNECTION=sqlite\nDB_DATABASE=:memory:\n");
    echo "✓ .env creado<br>";
} else {
    echo "✓ .env existe<br>";
}

// Test 3: Directorios
echo "<br>TEST 3: Storage<br>";
$dirs = ['backend/storage/logs', 'backend/storage/framework/cache', 'backend/storage/framework/sessions', 'backend/storage/framework/views'];
foreach ($dirs as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        mkdir(__DIR__ . '/' . $dir, 0755, true);
    }
    echo "✓ $dir<br>";
}

// Test 4: Laravel START
echo "<br>TEST 4: LARAVEL_START<br>";
define('LARAVEL_START', microtime(true));
echo "✓ LARAVEL_START definido<br>";

// Test 5: Bootstrap - AQUÍ PUEDE FALLAR
echo "<br>TEST 5: Bootstrap Laravel<br>";
try {
    $app = require_once __DIR__ . '/backend/bootstrap/app.php';
    echo "✓ Laravel bootstrap OK: " . get_class($app) . "<br>";
} catch (Throwable $e) {
    echo "❌ BOOTSTRAP FALLA: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    die();
}

// Test 6: Request
echo "<br>TEST 6: Request<br>";
try {
    use Illuminate\Http\Request;
    $request = Request::capture();
    echo "✓ Request OK: " . $request->getPathInfo() . "<br>";
} catch (Throwable $e) {
    echo "❌ REQUEST FALLA: " . $e->getMessage() . "<br>";
    die();
}

echo "<br>🎉 TODOS LOS TESTS PASARON - Laravel está listo<br>";
echo "El problema debe estar en el handleRequest o las rutas específicas<br>";
?>