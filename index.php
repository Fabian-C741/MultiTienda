<?php
/**
 * Laravel Application Entry Point
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Autoloader
require_once __DIR__ . '/backend/vendor/autoload.php';

// Bootstrap Laravel Application
$app = require_once __DIR__ . '/backend/bootstrap/app.php';

// Handle the incoming request
$app->handleRequest(Request::capture())->send();
