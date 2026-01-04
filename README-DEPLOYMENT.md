# 🚀 MultiTienda Professional Deployment Process

## 🎯 Overview

Este sistema implementa un workflow profesional de desarrollo y deployment para MultiTienda, eliminando los problemas de "cambios directos a producción" y "errores descubiertos en producción".

## 🏗️ Infrastructure Setup

### 1. Hostinger Configuration

**Crear subdominios:**
- `staging.multitienda.kcrsf.com` → `/staging` folder
- `multitienda.kcrsf.com` → `/public_html` folder (production)

**Directory structure on server:**
```
/home/u464516792/domains/multitienda.kcrsf.com/
├── public_html/     ← PRODUCTION (main branch)
└── staging/         ← STAGING (staging branch)
```

### 2. Git Workflow

```
main       ← Production releases only
  ↑
staging    ← Pre-production testing
  ↑
develop    ← Development integration
  ↑
feature/*  ← Individual features
```

## 🔄 Development Process

### Step 1: Feature Development
```bash
git checkout develop
git pull origin develop
git checkout -b feature/nueva-funcionalidad

# Make changes...
git add .
git commit -m "feat: nueva funcionalidad"
git push origin feature/nueva-funcionalidad
```

### Step 2: Testing in Staging
```bash
git checkout staging
git merge feature/nueva-funcionalidad
git push origin staging

# Deploy to staging
.\deploy.ps1 staging
# Upload to staging.multitienda.kcrsf.com

# Run automated tests
.\test-deployment.sh staging
# OR visit: https://staging.multitienda.kcrsf.com/health-check.php
```

### Step 3: Production Deploy (Only if staging tests pass)
```bash
git checkout main
git merge staging
git push origin main

# Deploy to production
.\deploy.ps1 production
# Upload to multitienda.kcrsf.com
```

## 🧪 Testing & Validation

### Automated Health Checks
- **Local testing:** `php health-check.php`
- **Remote testing:** Visit `/health-check.php` on any environment
- **JSON API:** Add `?json=1` for automated parsing

### Test Coverage
- ✅ File structure validation
- ✅ PHP syntax checking
- ✅ Route accessibility
- ✅ Content validation
- ✅ Basic security checks

## 📦 Deployment Scripts

| Script | Purpose | Usage |
|--------|---------|-------|
| `deploy.ps1` | Windows deployment | `.\deploy.ps1 staging` |
| `deploy.sh` | Linux deployment | `./deploy.sh staging` |
| `test-deployment.sh` | Remote testing | `./test-deployment.sh staging` |
| `health-check.php` | System validation | Visit `/health-check.php` |

## 🔒 Security Features

- **File exclusion** - Dev files never reach production
- **Permission checks** - Validates file accessibility
- **Error disclosure protection** - Checks for information leaks
- **Sensitive file protection** - Validates .env security

## ⚡ Quick Start

1. **Setup branches** (already done):
   ```bash
   git checkout develop  # For new features
   ```

2. **Deploy to staging**:
   ```bash
   .\deploy.ps1 staging
   # Upload files to staging.multitienda.kcrsf.com
   ```

3. **Test staging**:
   - Visit: https://staging.multitienda.kcrsf.com/health-check.php
   - Verify all functionality works

4. **Deploy to production** (only if staging tests pass):
   ```bash
   .\deploy.ps1 production
   # Upload files to multitienda.kcrsf.com
   ```

## 🚨 Emergency Rollback

If production breaks:
1. Keep previous deployment package
2. Upload previous version to production
3. Fix issues in staging first
4. Re-deploy when fixed

## 📊 Benefits

- ✅ **Zero downtime deployments**
- ✅ **Pre-production testing**
- ✅ **Automated validation**
- ✅ **Version control**
- ✅ **Rollback capability**
- ✅ **Professional workflow**

---

**¿Preguntas?** El sistema está diseñado para eliminar sorpresas en producción. Siempre prueba en staging primero.