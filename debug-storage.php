<?php
// Test específico para verificar el storage system
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Test Storage System</h1>";

try {
    echo "<p><strong>1. Verificando directorio data...</strong></p>";
    $dataDir = 'data';
    
    if (!is_dir($dataDir)) {
        echo "<p>❌ Directorio 'data' no existe</p>";
        if (mkdir($dataDir, 0755, true)) {
            echo "<p>✅ Directorio 'data' creado</p>";
        } else {
            throw new Exception("No se pudo crear el directorio data");
        }
    } else {
        echo "<p>✅ Directorio 'data' existe</p>";
    }
    
    // Verificar permisos
    if (is_writable($dataDir)) {
        echo "<p>✅ Directorio 'data' es escribible</p>";
    } else {
        echo "<p>❌ Directorio 'data' NO es escribible</p>";
    }
    
    echo "<p><strong>2. Verificando archivo users.json...</strong></p>";
    $usersFile = $dataDir . '/users.json';
    
    if (file_exists($usersFile)) {
        echo "<p>✅ users.json existe</p>";
        $content = file_get_contents($usersFile);
        echo "<p>✅ Contenido leído (" . strlen($content) . " bytes)</p>";
        
        $users = json_decode($content, true);
        if ($users === null) {
            echo "<p>❌ Error al decodificar JSON: " . json_last_error_msg() . "</p>";
        } else {
            echo "<p>✅ JSON decodificado correctamente (" . count($users) . " usuarios)</p>";
            
            foreach ($users as $user) {
                echo "<p>   - ID: " . $user['id'] . " | Email: " . $user['email'] . " | Role: " . $user['role'] . "</p>";
            }
        }
    } else {
        echo "<p>❌ users.json NO existe</p>";
    }
    
    echo "<p><strong>3. Inicializando JsonStorage...</strong></p>";
    require_once 'storage.php';
    $storage = new JsonStorage();
    echo "<p>✅ JsonStorage inicializado</p>";
    
    echo "<p><strong>4. Cargando usuarios con load()...</strong></p>";
    $loadedUsers = $storage->load('users');
    echo "<p>✅ Usuarios cargados: " . count($loadedUsers) . "</p>";
    
    echo "<p><strong>5. Probando find()...</strong></p>";
    $adminUser = $storage->find('users', 'email', 'admin@multitienda.com');
    if ($adminUser) {
        echo "<p>✅ Admin encontrado:</p>";
        echo "<pre style='background: #e8f5e8; padding: 1rem;'>";
        print_r($adminUser);
        echo "</pre>";
    } else {
        echo "<p>❌ Admin NO encontrado</p>";
    }
    
    echo "<p><strong>6. Verificando si users.json se recreó...</strong></p>";
    if (file_exists($usersFile)) {
        $newContent = file_get_contents($usersFile);
        echo "<p>✅ users.json existe ahora (" . strlen($newContent) . " bytes)</p>";
        
        if ($content !== $newContent) {
            echo "<p>🔄 Contenido cambió después de inicializar JsonStorage</p>";
        } else {
            echo "<p>✅ Contenido se mantuvo igual</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'><strong>FATAL ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Archivos en directorio actual:</strong></p>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "<p>- $file" . (is_dir($file) ? ' (directorio)' : '') . "</p>";
    }
}

if (is_dir('data')) {
    echo "<p><strong>Archivos en directorio data:</strong></p>";
    $dataFiles = scandir('data');
    foreach ($dataFiles as $file) {
        if ($file !== '.' && $file !== '..') {
            $size = filesize('data/' . $file);
            echo "<p>- $file ($size bytes)</p>";
        }
    }
}
?>