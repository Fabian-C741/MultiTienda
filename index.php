<?php
/**
 * MultiTienda - Diagnóstico completo
 */

echo "<h1>🔍 Diagnóstico MultiTienda</h1>";

// 1. Verificar directorio backend
$backendPath = __DIR__ . '/backend';
echo "<p>✓ Ruta backend: " . $backendPath . "</p>";

if (!is_dir($backendPath)) {
    die('<p>❌ Error: Backend directory not found</p>');
}
echo "<p>✓ Directorio backend existe</p>";

// 2. Verificar vendor
$vendorPath = $backendPath . '/vendor/autoload.php';
echo "<p>✓ Checking vendor: " . $vendorPath . "</p>";

if (!file_exists($vendorPath)) {
    die('<p>❌ Error: Laravel dependencies not installed</p><p>Necesitas ejecutar: cd backend && composer install</p>');
}
echo "<p>✓ Vendor autoload existe</p>";

// 3. Verificar Laravel bootstrap
$bootstrapPath = $backendPath . '/bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    die('<p>❌ Error: Laravel bootstrap not found</p>');
}
echo "<p>✓ Laravel bootstrap existe</p>";

// 4. Intentar cargar Laravel
try {
    echo "<p>🚀 Cargando Laravel...</p>";
    require_once $backendPath . '/public/index.php';
} catch (Exception $e) {
    echo '<p>❌ Error cargando Laravel: ' . $e->getMessage() . '</p>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
