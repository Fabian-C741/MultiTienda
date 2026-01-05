<?php
// Test básico de autenticación
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST DE AUTENTICACIÓN ===\n\n";

try {
    require_once 'storage.php';
    echo "✅ Storage cargado correctamente\n";
    
    $storage = new JsonStorage();
    echo "✅ JsonStorage inicializado\n";
    
    $users = $storage->load('users');
    echo "✅ Usuarios cargados: " . count($users) . "\n";
    
    foreach ($users as $user) {
        echo "   Usuario: " . $user['email'] . " (Role: " . $user['role'] . ")\n";
    }
    
    echo "\n--- PROBANDO BÚSQUEDA DE ADMIN ---\n";
    $adminUser = $storage->find('users', 'email', 'admin@multitienda.com');
    if ($adminUser) {
        echo "✅ Usuario admin encontrado: " . $adminUser['name'] . "\n";
        echo "   Password hash: " . substr($adminUser['password'], 0, 20) . "...\n";
        
        // Probar verificación de contraseña
        $passwordCheck = password_verify('admin123', $adminUser['password']);
        echo "   Password verification: " . ($passwordCheck ? "✅ CORRECTO" : "❌ INCORRECTO") . "\n";
    } else {
        echo "❌ Usuario admin NO encontrado\n";
    }
    
    echo "\n--- PROBANDO AUTH CLASS ---\n";
    require_once 'auth.php';
    echo "✅ Auth class cargada\n";
    
    $auth = auth();
    echo "✅ Auth instance creada\n";
    
    // Test de login
    $loginResult = $auth->login('admin@multitienda.com', 'admin123');
    echo "Login result: " . ($loginResult ? "✅ SUCCESS" : "❌ FAILED") . "\n";
    
    if ($loginResult) {
        $currentUser = $auth->getUser();
        if ($currentUser) {
            echo "✅ Usuario logueado: " . $currentUser['name'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";
?>