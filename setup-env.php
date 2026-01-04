<?php
// Script para crear .env en producción
$envContent = 'APP_NAME=MultiTienda
APP_ENV=production
APP_KEY=base64:bJ+rtI0X835KUSr9ekJQbJAnOEMH3tXkBR8GN8ea+fM=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=https://multitienda.kcrsf.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=single
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

SESSION_DRIVER=file
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

TENANCY_ENABLED=true
';

file_put_contents(__DIR__ . '/backend/.env', $envContent);
echo "✓ .env creado para producción\n";
echo "Configuración: cache=file, session=file, db=memory\n";
?>