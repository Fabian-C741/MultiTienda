<?php
/**
 * MultiTienda - Entry Point para Hostinger
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$backendPath = __DIR__ . '/backend';

// Maintenance mode
if (file_exists($maintenance = $backendPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader  
require $backendPath . '/vendor/autoload.php';

// Bootstrap Laravel
(require_once $backendPath . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
