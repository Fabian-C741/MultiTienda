# 🔒 SCRIPT DE LIMPIEZA POST-SEGURIDAD PARA WINDOWS (PowerShell)
# Uso: En PowerShell, navega a tu proyecto y ejecuta:
# powershell -ExecutionPolicy Bypass -File cleanup-local.ps1

Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "🔒 LIMPIEZA DE SEGURIDAD - MÁQUINA LOCAL (WINDOWS)" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en un repo git
if (-not (Test-Path ".git")) {
    Write-Host "❌ ERROR: No estás en un repositorio Git" -ForegroundColor Red
    exit 1
}

Write-Host "📍 Ubicación: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""

# Paso 1: Eliminar historial corrupto
Write-Host "1️⃣  Eliminando historial corrupto..." -ForegroundColor Magenta
Remove-Item ".git/refs/original" -Recurse -Force -ErrorAction SilentlyContinue
Write-Host "   ✅ Hecho" -ForegroundColor Green
Write-Host ""

# Paso 2: Reset hard
Write-Host "2️⃣  Haciendo reset hard a origin/main..." -ForegroundColor Magenta
git reset --hard origin/main | Out-Host
Write-Host "   ✅ Hecho" -ForegroundColor Green
Write-Host ""

# Paso 3: Fetch
Write-Host "3️⃣  Descargando cambios remoto..." -ForegroundColor Magenta
git fetch origin | Out-Host
Write-Host "   ✅ Hecho" -ForegroundColor Green
Write-Host ""

# Paso 4: Limpiar reflog
Write-Host "4️⃣  Limpiando reflog..." -ForegroundColor Magenta
git reflog expire --expire=now --all | Out-Host
Write-Host "   ✅ Hecho" -ForegroundColor Green
Write-Host ""

# Paso 5: Garbage collection
Write-Host "5️⃣  Ejecutando garbage collection agresivo..." -ForegroundColor Magenta
Write-Host "   (Esto puede tardar unos minutos...)" -ForegroundColor Yellow
git gc --aggressive | Out-Host
Write-Host "   ✅ Hecho" -ForegroundColor Green
Write-Host ""

# Verificación
Write-Host "🔍 VERIFICACIÓN:" -ForegroundColor Cyan
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "Búsqueda de data/users.json en historial:" -ForegroundColor Yellow

$found = git log --all --oneline -- data/users.json 2>$null
if ($found) {
    Write-Host "❌ ADVERTENCIA: Archivo aún en historial" -ForegroundColor Red
    Write-Host $found -ForegroundColor Yellow
} else {
    Write-Host "✅ Archivo eliminado del historial (CORRECTO)" -ForegroundColor Green
}
Write-Host ""

Write-Host "Detalles del repositorio:" -ForegroundColor Yellow
git count-objects -v | Out-Host
Write-Host ""

Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "✅ LIMPIEZA COMPLETADA" -ForegroundColor Green
Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

$status = git branch -vv | Where-Object { $_ -match "^\*" }
Write-Host "Estado: $status" -ForegroundColor Green

Write-Host ""
Write-Host "👥 Próximo paso: Actualizar credenciales en producción" -ForegroundColor Magenta
Write-Host "📖 Ver archivo: POST_CLEANUP_GUIDE.md" -ForegroundColor Magenta
