<?php
$timestamp = date('Y-m-d H:i:s');
$commitId = 'ba9fe71'; // Último commit ID
echo "=== VERIFICACIÓN DESPLIEGUE ===\n";
echo "Timestamp: $timestamp\n";
echo "Commit: $commitId\n";
echo "PHP: " . phpversion() . "\n";
echo "Directorio: " . __DIR__ . "\n";
echo "URL: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";

// Verificar si los archivos existen
$files = [
    'index.php',
    'test.php', 
    'backend/composer.json',
    'backend/vendor/autoload.php',
    'backend/bootstrap/app.php',
    '.htaccess'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ $file existe\n";
    } else {
        echo "❌ $file NO EXISTE\n";
    }
}

// Verificar contenido de index.php
echo "\n=== CONTENIDO INDEX.PHP ===\n";
$indexContent = file_get_contents(__DIR__ . '/index.php');
echo substr($indexContent, 0, 200) . "...\n";
?>