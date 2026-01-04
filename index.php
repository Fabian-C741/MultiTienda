<?php
/**
 * MultiTienda - Entry Point
 */

// Verificar que el backend existe
$backendPath = __DIR__ . '/backend';
if (!is_dir($backendPath)) {
    die('Error: Backend directory not found');
}

// Verificar que vendor existe
if (!file_exists($backendPath . '/vendor/autoload.php')) {
    die('Error: Laravel dependencies not installed. Run: cd backend && composer install');
}

// Cargar Laravel directamente
require_once $backendPath . '/public/index.php';
