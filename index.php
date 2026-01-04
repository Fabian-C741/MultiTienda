<?php
/**
 * MultiTienda - Punto de entrada principal
 * Laravel Application Handler
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ruta del backend
$backendPath = __DIR__ . '/backend';

// Verificar maintenance mode
if (file_exists($maintenance = $backendPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require $backendPath . '/vendor/autoload.php';

// Crear .env si no existe
$envPath = $backendPath . '/.env';
if (!file_exists($envPath)) {
    $envContent = "APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:dGVzdGluZ2tleWZvcm11bHRpdGllbmRh
APP_DEBUG=false
APP_URL=https://multitienda.kcrsf.com
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
LOG_CHANNEL=single
";
    file_put_contents($envPath, $envContent);
}

// Bootstrap Laravel Application
$app = require_once $backendPath . '/bootstrap/app.php';

// Handle the request
$app->handleRequest(Request::capture())->send();
