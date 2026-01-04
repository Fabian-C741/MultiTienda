<?php
// Diagnóstico básico para Error 500
echo "TEST PHP WORKS";

// Verificar sintaxis básica
$test = "OK";
echo " - Variables work: " . $test;

// Verificar directorio
$dir = __DIR__;
echo " - Directory: " . $dir;

// Verificar si existe backend
if (is_dir($dir . '/backend')) {
    echo " - Backend folder: EXISTS";
} else {
    echo " - Backend folder: MISSING";
}

// Verificar vendor
if (file_exists($dir . '/backend/vendor/autoload.php')) {
    echo " - Vendor: EXISTS";
} else {
    echo " - Vendor: MISSING";
}

echo " - PHP Version: " . phpversion();

// Test básico de require
try {
    if (file_exists($dir . '/backend/vendor/autoload.php')) {
        require_once $dir . '/backend/vendor/autoload.php';
        echo " - Autoload: LOADED";
    } else {
        echo " - Autoload: SKIPPED - FILE NOT FOUND";
    }
} catch (Exception $e) {
    echo " - Autoload ERROR: " . $e->getMessage();
}

echo " - END TEST";
?>
