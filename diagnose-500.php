<?php
// Diagnóstico específico del error 500 en login
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Debug Error 500</title></head><body>";
echo "<h1>🔧 Diagnóstico Error 500 Login Enterprise</h1>";

// Test 1: Verificar archivos core
echo "<h2>1. Verificando archivos core</h2>";
$coreFiles = ['auth.php', 'storage.php', 'modern-css.php', 'super-admin-functions.php'];
foreach ($coreFiles as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file existe (" . filesize($file) . " bytes)</p>";
    } else {
        echo "<p>❌ $file NO existe</p>";
    }
}

// Test 2: Probar carga individual de archivos
echo "<h2>2. Cargando archivos individualmente</h2>";
try {
    echo "<p>Cargando auth.php...</p>";
    ob_start();
    require_once 'auth.php';
    $output = ob_get_contents();
    ob_end_clean();
    echo "<p>✅ auth.php cargado (output: " . strlen($output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en auth.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal en auth.php: " . $e->getMessage() . "</p>";
}

try {
    echo "<p>Cargando storage.php...</p>";
    ob_start();
    require_once 'storage.php';
    $output = ob_get_contents();
    ob_end_clean();
    echo "<p>✅ storage.php cargado (output: " . strlen($output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en storage.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal en storage.php: " . $e->getMessage() . "</p>";
}

try {
    echo "<p>Cargando super-admin-functions.php...</p>";
    ob_start();
    require_once 'super-admin-functions.php';
    $output = ob_get_contents();
    ob_end_clean();
    echo "<p>✅ super-admin-functions.php cargado (output: " . strlen($output) . " bytes)</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en super-admin-functions.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal en super-admin-functions.php: " . $e->getMessage() . "</p>";
}

// Test 3: Verificar función handleError
echo "<h2>3. Verificando funciones críticas</h2>";
if (function_exists('handleError')) {
    echo "<p>✅ Función handleError existe</p>";
} else {
    echo "<p>❌ Función handleError NO existe</p>";
}

if (function_exists('auth')) {
    echo "<p>✅ Función auth() existe</p>";
    try {
        $authInstance = auth();
        echo "<p>✅ auth() se puede instanciar</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error al instanciar auth(): " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Función auth() NO existe</p>";
}

// Test 4: Probar modern-css.php específicamente
echo "<h2>4. Probando modern-css.php específicamente</h2>";
try {
    echo "<p>Incluyendo modern-css.php...</p>";
    ob_start();
    include 'modern-css.php';
    $cssOutput = ob_get_contents();
    ob_end_clean();
    echo "<p>✅ modern-css.php incluido (" . strlen($cssOutput) . " bytes de CSS)</p>";
    if (strlen($cssOutput) < 100) {
        echo "<p>⚠️ Output muy pequeño, posible error</p>";
        echo "<pre>" . htmlspecialchars($cssOutput) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en modern-css.php: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal en modern-css.php: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}

// Test 5: Simular POST completo como en index.php
echo "<h2>5. Simulando proceso de login completo</h2>";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'admin@multitienda.com';
$_POST['password'] = 'admin123';

try {
    echo "<p>Verificando si está logueado...</p>";
    if (auth()->isLoggedIn()) {
        echo "<p>⚠️ Ya está logueado</p>";
    } else {
        echo "<p>✅ No está logueado, continuar</p>";
    }
    
    echo "<p>Procesando POST...</p>";
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<p>Email: $email</p>";
    echo "<p>Password: [oculto]</p>";
    
    echo "<p>Intentando login...</p>";
    $loginResult = auth()->login($email, $password);
    
    if ($loginResult) {
        echo "<p>✅ Login exitoso</p>";
        
        $user = auth()->getUser();
        if ($user) {
            echo "<p>✅ Usuario obtenido: " . $user['name'] . "</p>";
            $redirectUrl = ($user['role'] === 'super_admin' ? '/super-admin' : '/admin');
            echo "<p>✅ URL redirect: $redirectUrl</p>";
            
            // Verificar headers
            if (!headers_sent()) {
                echo "<p>✅ Headers OK, podría redirigir</p>";
            } else {
                echo "<p>⚠️ Headers ya enviados</p>";
            }
        } else {
            echo "<p>❌ No se pudo obtener usuario después del login</p>";
        }
    } else {
        echo "<p>❌ Login falló</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en proceso de login: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p>❌ Fatal en proceso de login: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 6: Verificar exactamente el punto donde falla
echo "<h2>6. Probando el HTML completo del login</h2>";
try {
    echo "<p>Generando HTML completo...</p>";
    
    // Buffer para capturar cualquier output
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - MultiTienda Pro</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <?php include 'modern-css.php'; ?>
    </head>
    <body>
        <p>Test HTML generado correctamente</p>
    </body>
    </html>
    <?php
    
    $htmlOutput = ob_get_contents();
    ob_end_clean();
    
    echo "<p>✅ HTML generado correctamente (" . strlen($htmlOutput) . " bytes)</p>";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "<p>❌ Error generando HTML: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
} catch (Error $e) {
    ob_end_clean();
    echo "<p>❌ Fatal generando HTML: " . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h2>🏁 Resumen del Diagnóstico</h2>";
echo "<p>Si alguno de los tests arriba falló, esa es la causa del error 500.</p>";
echo "<p>Busca los ❌ rojos para identificar el problema específico.</p>";

echo "</body></html>";
?>