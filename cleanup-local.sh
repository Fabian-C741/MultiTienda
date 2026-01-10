#!/bin/bash
# 🔒 SCRIPT DE LIMPIEZA POST-SEGURIDAD PARA MÁQUINAS LOCALES
# Ejecutar: bash cleanup-local.sh

set -e

echo "════════════════════════════════════════════"
echo "🔒 LIMPIEZA DE SEGURIDAD - MÁQUINA LOCAL"
echo "════════════════════════════════════════════"
echo ""

# Verificar que estamos en un repo git
if [ ! -d .git ]; then
    echo "❌ ERROR: No estás en un repositorio Git"
    exit 1
fi

echo "📍 Ubicación: $(pwd)"
echo ""

# Paso 1: Eliminar historial corrupto
echo "1️⃣  Eliminando historial corrupto..."
rm -rf .git/refs/original 2>/dev/null || true
echo "   ✅ Hecho"
echo ""

# Paso 2: Reset hard
echo "2️⃣  Haciendo reset hard a origin/main..."
git reset --hard origin/main
echo "   ✅ Hecho"
echo ""

# Paso 3: Fetch
echo "3️⃣  Descargando cambios remoto..."
git fetch origin
echo "   ✅ Hecho"
echo ""

# Paso 4: Limpiar reflog
echo "4️⃣  Limpiando reflog..."
git reflog expire --expire=now --all
echo "   ✅ Hecho"
echo ""

# Paso 5: Garbage collection
echo "5️⃣  Ejecutando garbage collection agresivo..."
git gc --aggressive
echo "   ✅ Hecho"
echo ""

# Verificación
echo "🔍 VERIFICACIÓN:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Búsqueda de data/users.json en historial:"
if git log --all --oneline -- data/users.json 2>/dev/null | head -5; then
    echo "❌ ADVERTENCIA: Archivo aún en historial"
else
    echo "✅ Archivo eliminado del historial (CORRECTO)"
fi
echo ""

echo "Detalles del repositorio:"
git count-objects -v
echo ""

echo "════════════════════════════════════════════"
echo "✅ LIMPIEZA COMPLETADA"
echo "════════════════════════════════════════════"
echo ""
echo "Estado: $(git branch -vv | grep '*')"
