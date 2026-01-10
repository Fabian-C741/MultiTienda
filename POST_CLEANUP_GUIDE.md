# 📋 GUÍA PRÁCTICA: POST-LIMPIEZA DE SEGURIDAD
## Cambiar credenciales, actualizar clientes y limpiar máquinas locales

---

## 1️⃣ EN PRODUCCIÓN/SERVIDOR: CAMBIAR CREDENCIALES

### Paso 1: Identifica dónde están tus credenciales actuales

#### Si usas un archivo `.env` en producción:
```bash
# En tu servidor, dentro del directorio de la aplicación
cd /home/tuusuario/tudominio.com/public_html
# O donde sea tu instalación

# Verifica el archivo .env
cat .env | grep -E "DB_|API_|MAIL_|TOKEN_"
```

#### Si usas variables de entorno del servidor:
```bash
# En cPanel/Hostinger: Panel > Variables de Entorno
# En AWS/DigitalOcean: Panel de administración > Environment Variables
# En Linux directo: nano /etc/environment
```

---

### Paso 2: Cambiar credenciales de Base de Datos

#### A. Si tienes acceso SSH/Terminal:
```bash
# 1. Conectarse a MySQL
mysql -u admin -p

# 2. Cambiar contraseña del usuario de BD
ALTER USER 'usuario_bd'@'localhost' IDENTIFIED BY 'NUEVA_CONTRASEÑA_MAS_SEGURA_2024';
FLUSH PRIVILEGES;
EXIT;

# 3. Editar archivo .env
nano /home/tuusuario/app/.env

# Cambiar:
# DE:
DB_PASSWORD=contraseña_antigua

# A:
DB_PASSWORD=NUEVA_CONTRASEÑA_MAS_SEGURA_2024

# Guardar: CTRL+O, Enter, CTRL+X
```

#### B. Si usas cPanel:
```
1. Login en cPanel
2. Ir a: MySQL Databases (o Bases de Datos MySQL)
3. Seleccionar usuario de BD
4. Cambiar contraseña
5. Copiar nueva contraseña
6. Ir a: File Manager
7. Editar .env con nuevo password
```

#### C. Si usas Hostinger:
```
1. Panel Hostinger > Base de Datos MySQL
2. Seleccionar usuario
3. Generar nueva contraseña
4. Copiar contraseña
5. Ir a: Gestor de Archivos
6. Abrir .env
7. Actualizar DB_PASSWORD
```

---

### Paso 3: Cambiar credenciales de API/Servicios

```bash
# Editar .env
nano .env

# Buscar y actualizar TODAS estas líneas con valores nuevos:
MAIL_USERNAME=nuevo_email@tudominio.com
MAIL_PASSWORD=nueva_contraseña_app
MAIL_FROM_ADDRESS=noreply@tudominio.com

API_KEY=nuevo_api_key_generado
API_SECRET=nuevo_api_secret_generado

SESSION_SECRET=nuevo_secret_aleatorio_generado
JWT_SECRET=nuevo_jwt_secret_generado

# Generar valores seguros (en Linux):
head -c 32 /dev/urandom | base64
# Ejecutar esto 5 veces para generar 5 secrets diferentes
```

---

### Paso 4: Cambiar credenciales de usuarios de la aplicación

```bash
# En tu servidor, ejecutar (si tienes Laravel):
cd /home/tuusuario/app
php artisan tinker

# Dentro de tinker:
$user = User::find(1);
$user->password = Hash::make('NUEVA_CONTRASEÑA_MAS_SEGURA');
$user->save();
exit;

# O para usuarios en JSON (tu caso):
# Editar: data/users.json
# Cambiar contraseña de admin con:
php -r "echo password_hash('NUEVA_CONTRASEÑA_MAS_SEGURA', PASSWORD_DEFAULT);"
# Copiar el hash generado y reemplazarlo en users.json
```

---

### Paso 5: Verificar que todo funciona

```bash
# Reiniciar servicios
sudo systemctl restart php-fpm
sudo systemctl restart nginx  # o apache2
sudo systemctl restart mysql

# Probar login con nuevas credenciales
# Ir a: https://tudominio.com/login
# Intentar con: admin@email.com / NUEVA_CONTRASEÑA
```

---

## 2️⃣ CLIENTES CONECTADOS: ACTUALIZAR HISTORIAL LOCAL

### Para TODOS los miembros del equipo que tengan clones locales:

#### Opción A: Método Simple (Recomendado)

```bash
# 1. Abrir terminal/PowerShell en tu proyecto
cd "d:\Proyectos 2\Tienda_online_multiplataformas"

# 2. Eliminar historial local corrupto
rm -r .git/refs/original  # En PowerShell: Remove-Item .git/refs/original -Recurse -Force

# 3. Hacer reset hard
git reset --hard origin/main

# 4. Hacer fetch del historial limpiado
git fetch origin

# 5. Verificar que está limpio
git log --all --oneline -- data/users.json
# Debería NO mostrar resultados

# ¡Listo!
```

#### Opción B: Si tienes cambios locales sin commit

```bash
# 1. Guardar cambios locales en un stash
git stash

# 2. Eliminar historial local corrupto
Remove-Item .git/refs/original -Recurse -Force

# 3. Hacer reset hard
git reset --hard origin/main

# 4. Recuperar cambios
git stash pop

# ¡Listo!
```

#### Opción C: Clonar nuevamente (Si hay conflictos)

```bash
# 1. Renombrar carpeta antigua
mv "d:\Proyectos 2\Tienda_online_multiplataformas" "d:\Proyectos 2\Tienda_online_multiplataformas_OLD"

# 2. Clonar repositorio limpio
git clone https://github.com/Fabian-C741/MultiTienda.git "d:\Proyectos 2\Tienda_online_multiplataformas"

# 3. Copiar archivos locales que necesites de la carpeta OLD
# (NO copiar .git, data/users.json, data/stores.json, etc.)

# 4. Eliminar carpeta OLD
rm -r "d:\Proyectos 2\Tienda_online_multiplataformas_OLD"
```

---

## 3️⃣ MÁQUINAS LOCALES: LIMPIEZA OPCIONAL (PERO RECOMENDADA)

### Ejecutar esto en CADA máquina local después del git pull:

```bash
# Abrir PowerShell en tu proyecto
cd "d:\Proyectos 2\Tienda_online_multiplataformas"

# Ejecutar limpieza completa
git reflog expire --expire=now --all
git gc --aggressive

# Verificar tamaño de .git antes y después
# Antes: más grande (contiene historial "basura")
# Después: más pequeño (historial comprimido)
```

#### En PowerShell específicamente:
```powershell
Set-Location "d:\Proyectos 2\Tienda_online_multiplataformas"

# Limpiar reflog
git reflog expire --expire=now --all

# Garbage collection
git gc --aggressive --force

# Verificar resultado
git count-objects -v

# Debería mostrar algo como:
# count: xxx
# size: xxx
# in-pack: xxx
# packs: x
# size-pack: xxxx
# prune-packable: 0
# garbage: 0
```

---

## 📋 CHECKLIST DE EJECUCIÓN

### En Producción:
```
☐ Cambiar contraseña BD (cPanel/Hostinger/SSH)
☐ Actualizar .env con nueva contraseña
☐ Cambiar contraseña usuario admin (JSON o BD)
☐ Generar nuevos API_KEY y API_SECRET
☐ Generar nuevos MAIL_PASSWORD y otros secrets
☐ Reiniciar servicios (PHP, MySQL, Nginx/Apache)
☐ Probar login con nuevas credenciales
☐ Verificar que la aplicación funciona
```

### Para Todo el Equipo:
```
☐ Cada miembro: git reset --hard origin/main
☐ Cada miembro: git fetch origin
☐ Cada miembro: Verificar git log -- data/users.json (sin resultados)
☐ Cada miembro: git reflog expire --expire=now --all
☐ Cada miembro: git gc --aggressive
☐ Comunicar a todos que está listo
```

---

## ⚠️ ADVERTENCIAS

1. **NO subas .env a Git** - Usa .env.example como template
2. **NO compartas contraseñas en chat/email** - Usa gestor de contraseñas (1Password, LastPass)
3. **En producción**: Hacer estos cambios en horario bajo carga
4. **Hacer backup** antes de cambiar BD contraseña
5. **Testear** todo después de cambios

---

## 🆘 SI ALGO SALE MAL

```bash
# Si tu repositorio quedó corrupto:
git reflog
git reset --hard HEAD@{numero_anterior}

# Si necesitas recuperar todo:
git clone https://github.com/Fabian-C741/MultiTienda.git nuevo_clon
```

---

## 📞 COMANDOS RÁPIDOS (COPIAR Y PEGAR)

### Para cada miembro del equipo (PowerShell):
```powershell
Set-Location "d:\Proyectos 2\Tienda_online_multiplataformas"
Remove-Item .git/refs/original -Recurse -Force -ErrorAction SilentlyContinue
git reset --hard origin/main
git fetch origin
git reflog expire --expire=now --all
git gc --aggressive
Write-Host "✅ Historial local limpiado y sincronizado"
```

### Para el servidor (Terminal/SSH):
```bash
cd /home/usuario/app

# Cambiar BD password
mysql -u admin -p <<EOF
ALTER USER 'usuario_bd'@'localhost' IDENTIFIED BY 'nueva_contraseña';
FLUSH PRIVILEGES;
EOF

# Actualizar .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=nueva_contraseña/' .env
sed -i 's/API_KEY=.*/API_KEY=nuevo_key/' .env
sed -i 's/API_SECRET=.*/API_SECRET=nuevo_secret/' .env

# Reiniciar
sudo systemctl restart php-fpm nginx mysql
```

---

¡Con esto tu aplicación está 100% segura! 🔒
