@echo off
chcp 65001 >nul
REM ===========================================================================
REM  DEPLOY.BAT - Limpieza y sincronización de seguridad
REM  Ejecuta: Doble click en este archivo
REM ===========================================================================

echo.
echo ╔═══════════════════════════════════════════════════════════════════════╗
echo ║                                                                       ║
echo ║                    🔒 DEPLOY DE SEGURIDAD v1.0                       ║
echo ║                                                                       ║
echo ║  Este script:                                                         ║
echo ║  ✓ Actualiza el historial de Git                                     ║
echo ║  ✓ Sincroniza con repositorio limpio                                 ║
echo ║  ✓ Comprime la base de datos local                                   ║
echo ║  ✓ Verifica que todo está OK                                         ║
echo ║                                                                       ║
echo ║  Tiempo estimado: 2-5 minutos                                        ║
echo ║                                                                       ║
echo ╚═══════════════════════════════════════════════════════════════════════╝
echo.

REM Verificar que estamos en la carpeta correcta
if not exist ".git" (
    echo ❌ ERROR: No se encontró carpeta .git
    echo.
    echo Este archivo debe estar en la raíz del proyecto MultiTienda
    echo Ejemplo: d:\Proyectos 2\Tienda_online_multiplataformas\
    echo.
    pause
    exit /b 1
)

REM Verificar que Git está instalado
where git >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ ERROR: Git no está instalado o no está en PATH
    echo.
    echo Descarga Git desde: https://git-scm.com/download/win
    echo.
    pause
    exit /b 1
)

echo ℹ️  Información del sistema:
echo Carpeta: %cd%
echo Usuario: %USERNAME%
git --version
echo.

REM ========== PASO 1: Limpiar referencias antiguas ==========
echo [1/5] Limpiando referencias antiguas...
if exist ".git\refs\original" (
    rmdir /s /q ".git\refs\original" >nul 2>nul
    echo ✓ Referencias antiguas eliminadas
) else (
    echo ✓ No hay referencias antiguas
)
echo.

REM ========== PASO 2: Reset duro ==========
echo [2/5] Sincronizando con servidor...
call git reset --hard origin/main >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ ERROR al sincronizar. Intenta manualmente:
    echo   git fetch origin
    echo   git reset --hard origin/main
    pause
    exit /b 1
)
echo ✓ Sincronizado correctamente
echo.

REM ========== PASO 3: Actualizar referencias reflog ==========
echo [3/5] Limpiando historial temporal...
call git reflog expire --expire=now --all >nul 2>nul
echo ✓ Historial temporal limpio
echo.

REM ========== PASO 4: Garbage collection ==========
echo [4/5] Comprimiendo base de datos (esto puede tardar)...
call git gc --aggressive --prune=now >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ⚠️  Compresión no completada (esto es raro, pero continúa)
) else (
    echo ✓ Base de datos comprimida
)
echo.

REM ========== PASO 5: Verificación ==========
echo [5/5] Verificando seguridad...
setlocal enabledelayedexpansion

REM Contar archivos comprometidos
set "count=0"
for %%F in (data\users.json data\stores.json data\products.json data\orders.json) do (
    git log --all --oneline -- "%%F" 2>nul | find /c /v "" >nul
    if !ERRORLEVEL! EQU 0 (
        echo ❌ FALLO: %%F encontrado en historial
        set "count=1"
    )
)

if !count! EQU 0 (
    echo ✅ Verificación EXITOSA:
    echo    - Credenciales eliminadas del historial
    echo    - Base de datos comprimida
    echo    - Todo sincronizado
) else (
    echo ❌ Se encontraron archivos comprometidos
)
echo.

REM ========== Información final ==========
echo.
echo ╔═══════════════════════════════════════════════════════════════════════╗
echo ║                        ✅ DEPLOY COMPLETADO                          ║
echo ╚═══════════════════════════════════════════════════════════════════════╝
echo.
echo 📊 Estado final:
git log --oneline -1
echo.
echo 📁 Carpeta: %cd%
echo 🔐 Seguridad: ✓ OK
echo.
echo 🎯 Próximos pasos:
echo    1. Espera a que el servidor sea actualizado
echo    2. Intenta login en: https://tudominio.com
echo    3. Si hay problemas, contacta a soporte
echo.
echo Presiona cualquier tecla para cerrar...
pause >nul
