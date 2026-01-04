<?php
/**
 * MultiTienda - Entry Point Directo
 * Sin redirects, sin complicaciones
 */

// Verificar que el backend existe
$backendPath = __DIR__ . '/backend';
if (!is_dir($backendPath)) {
    die('Error: Backend directory not found');
}

// Cargar Laravel directamente
require_once $backendPath . '/public/index.php';
