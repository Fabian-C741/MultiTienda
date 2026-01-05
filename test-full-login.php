<?php
// Test del flujo completo de login web real
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Login Real</title></head><body>";
echo "<h1>🔍 Test Flujo Completo Login Web</h1>";

// Simular exactamente las condiciones del formulario de login
$_SERVER['REQUEST_URI'] = '/login';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'admin@multitienda.com';
$_POST['password'] = 'admin123';

echo "<p><strong>Simulando POST a /login...</strong></p>";

// Buffer de salida para capturar todo
ob_start();

try {
    echo "<p>Paso 1: Variables de entorno...</p>";
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path = rtrim($path, '/');
    echo "<p>✅ Path: '$path'</p>";

    echo "<p>Paso 2: Cargando archivos base...</p>";
    require_once 'auth.php';
    require_once 'storage.php';
    echo "<p>✅ Auth y Storage cargados</p>";

    // Verificar si modern-css.php existe
    echo "<p>Paso 3: Verificando archivos requeridos...</p>";
    if (file_exists('modern-css.php')) {
        echo "<p>✅ modern-css.php existe</p>";
    } else {
        echo "<p>❌ modern-css.php NO existe</p>";
    }
    
    if (file_exists('landing.html')) {
        echo "<p>✅ landing.html existe</p>";
    } else {
        echo "<p>❌ landing.html NO existe</p>";
    }

    echo "<p>Paso 4: Verificando rutas...</p>";
    if ($path === '/login') {
        echo "<p>✅ Ruta /login detectada correctamente</p>";
        
        // Verificar si ya está logueado
        if (auth()->isLoggedIn()) {
            echo "<p>🔄 Ya logueado, debería redirigir</p>";
        } else {
            echo "<p>✅ No logueado, continuar con login</p>";
        }
        
        echo "<p>Paso 5: Procesando POST...</p>";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<p>✅ POST detectado</p>";
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            echo "<p>✅ Email: $email</p>";
            echo "<p>✅ Password: [oculto]</p>";
            
            echo "<p>Paso 6: Intentando login...</p>";
            if (auth()->login($email, $password)) {
                echo "<p>✅ Login exitoso</p>";
                
                $user = auth()->getUser();
                echo "<p>✅ Usuario obtenido: " . $user['name'] . "</p>";
                echo "<p>✅ Role: " . $user['role'] . "</p>";
                
                $redirectUrl = ($user['role'] === 'super_admin' ? '/super-admin' : '/admin');
                echo "<p>✅ URL redirect: $redirectUrl</p>";
                
                // Verificar headers
                if (!headers_sent()) {
                    echo "<p>✅ Headers no enviados, puede hacer redirect</p>";
                    echo "<p>🔄 Debería redirigir a: $redirectUrl</p>";
                } else {
                    echo "<p>❌ Headers ya enviados, no puede hacer redirect</p>";
                }
                
            } else {
                echo "<p>❌ Login falló</p>";
            }
        }
        
        echo "<p>Paso 7: Mostrando formulario de login...</p>";
        echo "<p>✅ Incluiría modern-css.php</p>";
        
        // Capturar la salida de modern-css.php
        ob_start();
        try {
            include 'modern-css.php';
            $cssOutput = ob_get_contents();
            ob_end_clean();
            echo "<p>✅ modern-css.php cargado (" . strlen($cssOutput) . " bytes)</p>";
        } catch (Exception $e) {
            ob_end_clean();
            echo "<p>❌ Error en modern-css.php: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p>❌ Ruta no es /login (es: '$path')</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ FATAL: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Mostrar cualquier output capturado
$output = ob_get_contents();
ob_end_clean();

if (!empty($output)) {
    echo "<h2>Output Buffer:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 1rem; white-space: pre-wrap;'>" . htmlspecialchars($output) . "</pre>";
}

echo "<hr>";
echo "<h2>🧪 Test Directo Simulando Navegador</h2>";
echo "<form method='POST' action='/login'>";
echo "<input type='hidden' name='test' value='1'>";
echo "<div><label>Email:</label><input type='email' name='email' value='admin@multitienda.com'></div>";
echo "<div><label>Password:</label><input type='password' name='password' value='admin123'></div>";
echo "<button type='submit'>🔐 Login Real</button>";
echo "</form>";

echo "</body></html>";
?>