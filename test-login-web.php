<?php
// Test simple de login web
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular condiciones del navegador web
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/login';
$_POST['email'] = 'admin@multitienda.com';
$_POST['password'] = 'admin123';

echo "<h1>Test de Login Web</h1>";

try {
    echo "<p>Incluyendo archivos base...</p>";
    require_once 'storage.php';
    require_once 'auth.php';
    
    echo "<p>✅ Archivos incluidos correctamente</p>";
    
    // Test del proceso de login
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<p>Datos recibidos: Email = $email, Password = [hidden]</p>";
    
    if (auth()->login($email, $password)) {
        echo "<p>✅ Login EXITOSO</p>";
        $user = auth()->getUser();
        echo "<p>Usuario logueado: " . $user['name'] . " (Role: " . $user['role'] . ")</p>";
    } else {
        echo "<p>❌ Login FALLÓ</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p>❌ FATAL ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Estado de la sesión:</h2>";
echo "<pre>";
print_r($_SESSION ?? ['NO SESSION']);
echo "</pre>";

echo "<p><strong>Test completado</strong></p>";
?>