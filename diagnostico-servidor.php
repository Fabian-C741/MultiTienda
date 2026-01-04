<?php
/**
 * 🔍 DIAGNÓSTICO REAL - ¿Qué está pasando en el servidor?
 * Este archivo nos dirá exactamente qué hay en producción
 */

echo "<h1>🔍 Diagnóstico del Servidor Real</h1>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Ruta solicitada:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<hr>";

echo "<h2>📁 Archivos en el directorio raíz:</h2>";
$files = scandir(__DIR__);
echo "<ul>";
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $path = __DIR__ . '/' . $file;
        $isDir = is_dir($path);
        $size = $isDir ? '(directorio)' : '(' . filesize($path) . ' bytes)';
        $permission = substr(sprintf('%o', fileperms($path)), -4);
        echo "<li><strong>$file</strong> $size - Permisos: $permission</li>";
    }
}
echo "</ul>";

echo "<h2>📄 Contenido de archivos clave:</h2>";

// Verificar .htaccess
echo "<h3>.htaccess:</h3>";
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/.htaccess')) . "</pre>";
} else {
    echo "<p style='color:red;'>❌ .htaccess NO EXISTE</p>";
}

// Verificar index.php
echo "<h3>index.php:</h3>";
if (file_exists(__DIR__ . '/index.php')) {
    echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/index.php')) . "</pre>";
} else {
    echo "<p style='color:red;'>❌ index.php NO EXISTE</p>";
}

// Verificar multitienda-simple.php
echo "<h3>multitienda-simple.php:</h3>";
if (file_exists(__DIR__ . '/multitienda-simple.php')) {
    echo "<p style='color:green;'>✅ multitienda-simple.php EXISTE</p>";
    $content = file_get_contents(__DIR__ . '/multitienda-simple.php');
    echo "<p>Tamaño: " . strlen($content) . " caracteres</p>";
    echo "<p>Primeras 200 chars: <code>" . htmlspecialchars(substr($content, 0, 200)) . "...</code></p>";
} else {
    echo "<p style='color:red;'>❌ multitienda-simple.php NO EXISTE</p>";
}

echo "<h2>🔧 Variables del servidor:</h2>";
echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
echo "<li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</li>";
echo "<li><strong>Query String:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'none') . "</li>";
echo "</ul>";

echo "<h2>🌐 Test de rutas:</h2>";
$testRoutes = ['/', '/central', '/central/tenants', '/central/stats'];
foreach ($testRoutes as $route) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $route;
    echo "<p><strong>$route:</strong> <a href='$url' target='_blank'>$url</a></p>";
}

echo "<h2>💡 Diagnóstico automático:</h2>";
$issues = [];

if (!file_exists(__DIR__ . '/.htaccess')) {
    $issues[] = "❌ Falta archivo .htaccess - Las rutas no funcionarán";
}

if (!file_exists(__DIR__ . '/multitienda-simple.php')) {
    $issues[] = "❌ Falta multitienda-simple.php - El sistema principal no existe";
}

if (file_exists(__DIR__ . '/index.php')) {
    $indexContent = file_get_contents(__DIR__ . '/index.php');
    if (strpos($indexContent, 'Laravel') !== false) {
        $issues[] = "⚠️ index.php aún contiene código de Laravel - Puede causar conflictos";
    }
}

if (empty($issues)) {
    echo "<p style='color:green; font-weight:bold;'>✅ No se detectaron problemas obvios</p>";
} else {
    echo "<div style='background:#ffebee;padding:20px;border-radius:8px;'>";
    foreach ($issues as $issue) {
        echo "<p>$issue</p>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Diagnóstico generado: " . date('Y-m-d H:i:s') . "</small></p>";
?>