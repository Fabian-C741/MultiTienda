# 🚀 Guía de Deployment Profesional - MultiTienda

## 🎯 Problema Actual
- Cambios directos a producción sin testing
- No hay entorno de staging
- Errores se descubren en producción

## 🏗️ Solución: Entorno de Staging

### 1. **Configurar Subdominio de Staging**

En tu panel de Hostinger:
1. Ir a **Subdominios**
2. Crear: `staging.multitienda.kcrsf.com`
3. Apuntar a: `/staging` (nueva carpeta)

### 2. **Estructura de Carpetas en Hostinger**

```
/home/u464516792/domains/multitienda.kcrsf.com/
├── public_html/          ← PRODUCCIÓN (main branch)
└── staging/              ← STAGING (staging branch)
```

### 3. **Git Workflow Profesional**

```bash
main       ← Producción (solo releases)
  ↑
staging    ← Pre-producción (testing)
  ↑  
develop    ← Desarrollo activo
  ↑
feature/*  ← Features individuales
```

## 📋 Proceso de Desarrollo Seguro

### Paso 1: Desarrollo
```bash
git checkout develop
git pull origin develop
git checkout -b feature/nueva-funcionalidad
# Hacer cambios...
git add .
git commit -m "feat: nueva funcionalidad"
git push origin feature/nueva-funcionalidad
```

### Paso 2: Testing en Staging
```bash
git checkout staging
git merge feature/nueva-funcionalidad
git push origin staging
# ↑ Esto se despliega automáticamente en staging.multitienda.kcrsf.com
```

### Paso 3: Validación
- Probar en `staging.multitienda.kcrsf.com`
- Ejecutar tests automatizados
- Validar funcionalidad completa

### Paso 4: Producción (solo si staging OK)
```bash
git checkout main
git merge staging
git push origin main
# ↑ Esto se despliega automáticamente en multitienda.kcrsf.com
```

## 🧪 Scripts de Testing Automatizado

Los scripts se ejecutan automáticamente en staging antes de permitir merge a main.

## 🔄 Auto-deployment con Git Hooks

Configuración en Hostinger para deployment automático por branch.

## 📊 Monitoreo

- Health checks automáticos
- Alertas por email si algo falla
- Rollback automático en caso de errores