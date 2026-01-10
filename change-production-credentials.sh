#!/bin/bash
# 🔐 SCRIPT PARA CAMBIAR CREDENCIALES EN PRODUCCIÓN (LINUX/MAC)
# Uso: bash change-production-credentials.sh

set -e

echo "════════════════════════════════════════════"
echo "🔐 CAMBIO DE CREDENCIALES - PRODUCCIÓN"
echo "════════════════════════════════════════════"
echo ""

# Verificar que es producción
read -p "⚠️  ADVERTENCIA: Esto cambiará credenciales reales. ¿Continuar? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "❌ Cancelado"
    exit 1
fi

echo ""
echo "1️⃣  INFORMACIÓN DEL SERVIDOR"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "Introduce la ruta de tu aplicación (ej: /home/usuario/public_html): " APP_PATH

if [ ! -d "$APP_PATH" ]; then
    echo "❌ ERROR: La carpeta $APP_PATH no existe"
    exit 1
fi

echo "✅ Carpeta encontrada: $APP_PATH"
cd "$APP_PATH"
echo ""

# Hacer backup del .env
echo "2️⃣  HACIENDO BACKUP"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Backup creado"
echo ""

# Cambiar contraseña BD
echo "3️⃣  CAMBIO DE CONTRASEÑA DE BASE DE DATOS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "Usuario de BD (ej: usuario_bd): " DB_USER
read -p "Host de BD (ej: localhost): " DB_HOST
read -sp "Contraseña ACTUAL de BD: " CURRENT_DB_PASS
echo ""
read -sp "Contraseña NUEVA de BD (mínimo 12 caracteres): " NEW_DB_PASS
echo ""

# Validar contraseña
if [ ${#NEW_DB_PASS} -lt 12 ]; then
    echo "❌ ERROR: La contraseña debe tener mínimo 12 caracteres"
    exit 1
fi

# Cambiar contraseña en MySQL
mysql -u root -p -h "$DB_HOST" <<EOF
ALTER USER '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$NEW_DB_PASS';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo "✅ Contraseña de BD cambiada"
    # Actualizar .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$NEW_DB_PASS/" .env
    echo "✅ .env actualizado con nueva contraseña"
else
    echo "❌ ERROR al cambiar contraseña de BD"
    exit 1
fi
echo ""

# Cambiar API Key
echo "4️⃣  GENERANDO NUEVAS CLAVES API"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
NEW_API_KEY=$(head -c 32 /dev/urandom | base64 | tr -d '=+/' | cut -c1-32)
NEW_API_SECRET=$(head -c 32 /dev/urandom | base64 | tr -d '=+/' | cut -c1-32)

sed -i "s/API_KEY=.*/API_KEY=$NEW_API_KEY/" .env
sed -i "s/API_SECRET=.*/API_SECRET=$NEW_API_SECRET/" .env

echo "✅ API_KEY: $NEW_API_KEY"
echo "✅ API_SECRET: $NEW_API_SECRET"
echo ""

# Cambiar sesión secret
echo "5️⃣  GENERANDO NUEVO SESSION SECRET"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
NEW_SESSION_SECRET=$(head -c 32 /dev/urandom | base64)
sed -i "s/SESSION_SECRET=.*/SESSION_SECRET=$NEW_SESSION_SECRET/" .env

echo "✅ SESSION_SECRET actualizado"
echo ""

# Cambiar email password (opcional)
echo "6️⃣  ACTUALIZAR CREDENCIALES DE EMAIL (opcional)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "¿Deseas cambiar MAIL_PASSWORD? (s/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    read -sp "Nueva contraseña de email: " NEW_MAIL_PASS
    echo ""
    sed -i "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$NEW_MAIL_PASS/" .env
    echo "✅ MAIL_PASSWORD actualizado"
fi
echo ""

# Reiniciar servicios
echo "7️⃣  REINICIANDO SERVICIOS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "¿Reiniciar servicios (PHP/MySQL/Nginx)? (s/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "Reiniciando PHP-FPM..."
    sudo systemctl restart php-fpm || sudo service php-fpm restart
    
    echo "Reiniciando MySQL..."
    sudo systemctl restart mysql || sudo service mysql restart
    
    echo "Reiniciando Nginx..."
    sudo systemctl restart nginx || sudo service nginx restart
    
    echo "✅ Servicios reiniciados"
fi
echo ""

# Verificar
echo "8️⃣  VERIFICACIÓN"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Nuevas credenciales en .env:"
grep -E "DB_PASSWORD|API_KEY|API_SECRET" .env | head -3
echo ""

echo "════════════════════════════════════════════"
echo "✅ CAMBIO DE CREDENCIALES COMPLETADO"
echo "════════════════════════════════════════════"
echo ""
echo "📝 RESUMEN:"
echo "   ✅ Base de datos: Contraseña actualizada"
echo "   ✅ API Keys: Regeneradas"
echo "   ✅ Secrets: Actualizados"
echo "   ✅ .env: Actualizado"
echo "   ✅ Servicios: Reiniciados"
echo ""
echo "📌 IMPORTANTE:"
echo "   1. Guarda la contraseña de BD en lugar seguro"
echo "   2. Prueba que el login funciona"
echo "   3. Verifica logs: tail -f var/log/php-fpm.log"
echo ""
