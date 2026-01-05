<?php
// DIAGNÓSTICO RÁPIDO - Error en páginas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Diagnóstico Rápido</h1>";

echo "<h2>1. Verificando archivos críticos</h2>";
$files = [
    'login-enterprise-robusto.php',
    'index.php',
    'auth.php', 
    'storage.php',
    'enterprise-design.css',
    'analytics-engine.js'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p>✅ $file - $size bytes</p>";
        
        // Verificar si es PHP y tiene errores de sintaxis
        if (str_ends_with($file, '.php')) {
            $content = file_get_contents($file);
            if (empty($content)) {
                echo "<p>❌ $file está VACÍO</p>";
            } else {
                // Verificar sintaxis básica
                $firstLine = strtok($content, "\n");
                if (!str_starts_with($firstLine, '<?php')) {
                    echo "<p>⚠️ $file no empieza con <?php</p>";
                }
            }
        }
    } else {
        echo "<p>❌ $file NO EXISTE</p>";
    }
}

echo "<h2>2. Test rápido de storage</h2>";
try {
    require_once 'storage.php';
    $storage = new JsonStorage();
    $users = $storage->load('users');
    echo "<p>✅ Storage OK - " . count($users) . " usuarios</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en storage: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Test de auth</h2>";
try {
    require_once 'auth.php';
    $auth = auth();
    echo "<p>✅ Auth cargado OK</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en auth: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Verificando directorio data</h2>";
if (is_dir('data')) {
    echo "<p>✅ Directorio data existe</p>";
    $dataFiles = scandir('data');
    foreach ($dataFiles as $file) {
        if ($file != '.' && $file != '..') {
            $size = filesize('data/' . $file);
            echo "<p>   - $file: $size bytes</p>";
        }
    }
} else {
    echo "<p>❌ Directorio data NO EXISTE</p>";
}

echo "<h2>5. PHP Info básico</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Max Execution Time: " . ini_get('max_execution_time') . "</p>";

echo "<h2>6. Test simple de login</h2>";
echo '<form method="POST">';
echo '<input type="email" name="email" value="admin@multitienda.com" placeholder="Email">';
echo '<input type="password" name="password" value="admin123" placeholder="Password">';  
echo '<button type="submit">Test Login</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p><strong>Probando login...</strong></p>";
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (function_exists('auth')) {
        if (auth()->login($email, $password)) {
            echo "<p>✅ Login exitoso</p>";
            $user = auth()->getUser();
            echo "<p>Usuario: " . $user['name'] . "</p>";
        } else {
            echo "<p>❌ Login falló</p>";
        }
    } else {
        echo "<p>❌ Función auth() no existe</p>";
    }
}
?>