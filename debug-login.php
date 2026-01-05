<?php
// Test directo de la página de login
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Buffer de salida para capturar cualquier error
ob_start();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Test Login Debug</title></head><body>";
echo "<h1>🔍 Debug Login MultiTienda</h1>";

try {
    echo "<p><strong>Paso 1:</strong> Verificando archivos...</p>";
    
    if (!file_exists('auth.php')) {
        throw new Exception("❌ auth.php no encontrado");
    }
    echo "<p>✅ auth.php existe</p>";
    
    if (!file_exists('storage.php')) {
        throw new Exception("❌ storage.php no encontrado");
    }
    echo "<p>✅ storage.php existe</p>";
    
    echo "<p><strong>Paso 2:</strong> Cargando sistema...</p>";
    require_once 'storage.php';
    echo "<p>✅ storage.php cargado</p>";
    
    require_once 'auth.php';
    echo "<p>✅ auth.php cargado</p>";
    
    echo "<p><strong>Paso 3:</strong> Verificando datos de usuarios...</p>";
    $storage = new JsonStorage();
    $users = $storage->load('users');
    echo "<p>✅ Usuarios en BD: " . count($users) . "</p>";
    
    foreach ($users as $user) {
        echo "<p>   - " . $user['email'] . " (" . $user['role'] . ")</p>";
    }
    
    echo "<p><strong>Paso 4:</strong> Simulando POST de login...</p>";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['email'] = 'admin@multitienda.com';
    $_POST['password'] = 'admin123';
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    echo "<p>Email: $email</p>";
    echo "<p>Password: [oculto]</p>";
    
    echo "<p><strong>Paso 5:</strong> Intentando login...</p>";
    $loginResult = auth()->login($email, $password);
    
    if ($loginResult) {
        echo "<p>✅ <strong>LOGIN EXITOSO!</strong></p>";
        
        $user = auth()->getUser();
        if ($user) {
            echo "<p>✅ Usuario recuperado: " . $user['name'] . "</p>";
            echo "<p>✅ Role: " . $user['role'] . "</p>";
            
            // Simular redirección
            if ($user['role'] === 'super_admin') {
                echo "<p>🔄 Debería redirigir a: /super-admin</p>";
            } else {
                echo "<p>🔄 Debería redirigir a: /admin</p>";
            }
        } else {
            echo "<p>❌ No se pudo recuperar datos del usuario después del login</p>";
        }
    } else {
        echo "<p>❌ <strong>LOGIN FALLÓ!</strong></p>";
        
        // Debug adicional
        $testUser = $storage->find('users', 'email', $email);
        if ($testUser) {
            echo "<p>🔍 Usuario encontrado en BD</p>";
            echo "<p>🔍 Status: " . $testUser['status'] . "</p>";
            $passCheck = password_verify($password, $testUser['password']);
            echo "<p>🔍 Password check: " . ($passCheck ? 'CORRECTO' : 'INCORRECTO') . "</p>";
        } else {
            echo "<p>🔍 Usuario NO encontrado en BD</p>";
        }
    }
    
    echo "<p><strong>Paso 6:</strong> Estado de sesión...</p>";
    echo "<pre style='background: #f5f5f5; padding: 1rem; border-radius: 4px;'>";
    print_r($_SESSION ?? ['NO_SESSION' => true]);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<pre style='background: #fee; color: #c33; padding: 1rem;'>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ <strong>FATAL ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<pre style='background: #fee; color: #c33; padding: 1rem;'>" . $e->getTraceAsString() . "</pre>";
}

// Capturar cualquier output buffer error
$errors = ob_get_clean();
if (!empty($errors)) {
    echo "<h2>Output Buffer Errors:</h2>";
    echo "<pre style='background: #fee; color: #c33; padding: 1rem;'>$errors</pre>";
}

echo "<hr>";
echo "<h2>🧪 Test Directo del Formulario</h2>";
echo "<form method='POST' style='background: #f9f9f9; padding: 2rem; border-radius: 8px;'>";
echo "<div style='margin-bottom: 1rem;'>";
echo "<label>Email:</label><br>";
echo "<input type='email' name='email' value='admin@multitienda.com' style='width: 300px; padding: 0.5rem;'>";
echo "</div>";
echo "<div style='margin-bottom: 1rem;'>";
echo "<label>Password:</label><br>";
echo "<input type='password' name='password' value='admin123' style='width: 300px; padding: 0.5rem;'>";
echo "</div>";
echo "<button type='submit' style='background: #007cba; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer;'>🔐 Test Login</button>";
echo "</form>";

echo "</body></html>";
?>