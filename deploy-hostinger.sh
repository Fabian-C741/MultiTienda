#!/bin/bash
# ===========================================
# Script de Deploy para Hostinger via Git
# ===========================================
# Este script se ejecuta después de cada git pull
# Ubicación en Hostinger: public_html/.git/hooks/post-receive
# O ejecutar manualmente via SSH

echo "🚀 Iniciando deploy..."

# Ir al directorio del proyecto
cd ~/public_html

# Instalar dependencias de Composer
echo "📦 Instalando dependencias PHP..."
cd backend
composer install --no-dev --optimize-autoloader --no-interaction

# Migraciones (solo base central, tenants se migran por separado)
echo "🗃️ Ejecutando migraciones..."
php artisan migrate --force

# Limpiar y cachear configuración
echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos de storage
echo "🔐 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Link de storage (si no existe)
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

cd ..

echo "✅ Deploy completado!"
echo "📅 $(date)"
