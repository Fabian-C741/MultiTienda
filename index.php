<?php
/**
 * Entry Point para Hostinger con Git Deploy
 * 
 * Copia este archivo a public_html/index.php en Hostinger
 * El repositorio se clona en public_html/ directamente
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ruta base del backend Laravel (relativa a public_html)
$backendPath = __DIR__ . '/backend';

// Maintenance mode
if (file_exists($maintenance = $backendPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require $backendPath . '/vendor/autoload.php';

// Bootstrap y manejar request
(require_once $backendPath . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
