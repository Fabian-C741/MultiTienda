# 🚀 Guía de Deploy en Hostinger via Git

## Requisitos Previos
- Plan Hostinger con acceso SSH y Git
- Repositorio en GitHub/GitLab/Bitbucket

---

## Paso 1: Subir a GitHub

```bash
# En tu máquina local
cd "d:\Proyectos 2\Tienda_online_multiplataformas"

# Inicializar Git (si no está)
git init

# Agregar archivos
git add .
git commit -m "Initial commit - Tienda Multi-tenant"

# Crear repo en GitHub y conectar
git remote add origin https://github.com/TU_USUARIO/tienda-multitenant.git
git branch -M main
git push -u origin main
```

---

## Paso 2: Configurar Git en Hostinger

### 2.1 Acceder a hPanel
1. Ir a **hPanel → Avanzado → Git**
2. Click en **"Crear nuevo repositorio"**

### 2.2 Configurar repositorio
- **URL del repositorio**: `https://github.com/TU_USUARIO/tienda-multitenant.git`
- **Rama**: `main`
- **Directorio**: `public_html` (dejar vacío para raíz)
- **Auto deploy**: ✅ Activar

### 2.3 Generar clave SSH (si es privado)
1. En hPanel → Git → **"Manage SSH Keys"**
2. Copiar la clave pública
3. Agregarla en GitHub → Settings → Deploy Keys

---

## Paso 3: Primera configuración en Hostinger

### 3.1 Conectar via SSH
```bash
ssh u123456789@servidor.hostinger.com
cd public_html
```

### 3.2 Instalar dependencias
```bash
cd backend
composer install --no-dev --optimize-autoloader
```

### 3.3 Configurar .env
```bash
cp .env.example.hostinger .env
nano .env
# Editar con tus datos reales
```

### 3.4 Generar key y optimizar
```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 3.5 Crear base de datos
1. hPanel → **Bases de datos MySQL**
2. Crear BD: `u123456789_central`
3. Crear usuario con permisos completos

### 3.6 Migrar
```bash
php artisan migrate
php artisan db:seed  # Si hay seeders
```

---

## Paso 4: Estructura Final en Hostinger

```
public_html/
├── .htaccess              ← Redirecciones
├── index.php              ← Entry point
├── backend/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   │   └── uploads/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── vendor/
├── frontend/              (opcional)
└── website/               (opcional)
```

---

## Paso 5: Deploys Automáticos

Cada vez que hagas `git push`, Hostinger:
1. Detecta el cambio (webhook)
2. Hace `git pull` automáticamente
3. Los archivos se actualizan

### Para ejecutar comandos post-deploy:
Conectar via SSH y ejecutar:
```bash
cd public_html
bash deploy-hostinger.sh
```

---

## 🔧 Comandos Útiles

### Limpiar cache
```bash
cd public_html/backend
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Crear nuevo tenant
```bash
php artisan tenant:create "Mi Tienda" mitienda
```

### Ver logs
```bash
tail -f storage/logs/laravel.log
```

### Migrar tenant específico
```bash
php artisan tenant:migrate --tenant=mitienda
```

---

## 🌐 URLs del Sistema

| Función | URL |
|---------|-----|
| Admin Central | `tudominio.com/admin` |
| Panel Tenant | `tudominio.com/tienda/{slug}/admin` |
| Storefront | `tudominio.com/tienda/{slug}` |
| API | `tudominio.com/api/v1/{slug}/products` |

---

## ⚠️ Solución de Problemas

### Error 500
```bash
# Verificar permisos
chmod -R 775 storage bootstrap/cache
# Ver log
cat storage/logs/laravel.log
```

### Cambios no se reflejan
```bash
php artisan config:clear
php artisan cache:clear
```

### Base de datos tenant no existe
```bash
php artisan tenant:migrate --tenant=SLUG
```
