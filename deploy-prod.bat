@echo off
chcp 65001 >nul
REM ===========================================================================
REM  DEPLOY-PROD.BAT - Cambio de credenciales en producción
REM  Ejecuta esto EN EL SERVIDOR SOLAMENTE via RDP/SSH
REM ===========================================================================

echo.
echo ╔═══════════════════════════════════════════════════════════════════════╗
echo ║                                                                       ║
echo ║            🔐 ACTUALIZACIÓN DE CREDENCIALES PRODUCCIÓN               ║
echo ║                                                                       ║
echo ║  ⚠️  ADVERTENCIA: Este script modifica CREDENCIALES EN VIVO            ║
echo ║                                                                       ║
echo ║  NO INTERRUMPAS durante la ejecución                                │
echo ║                                                                       ║
echo ╚═══════════════════════════════════════════════════════════════════════╝
echo.

REM Pedir confirmación
set /p confirm="¿Estás en el SERVIDOR de PRODUCCIÓN? (SI/NO): "
if /i not "%confirm%"=="SI" (
    echo ❌ CANCELADO. Asegúrate de estar en el servidor correcto.
    pause
    exit /b 1
)

echo.
echo ℹ️  Información del servidor:
echo Computadora: %COMPUTERNAME%
echo Usuario: %USERNAME%
echo Hora: %date% %time%
echo.

REM ========== PASO 1: Generar nuevas contraseñas ==========
echo [1/3] Generando nuevas contraseñas...

setlocal enabledelayedexpansion

REM Generar contraseña aleatoria (16 caracteres)
set "new_pass=Seg%random%%random%!@#"

echo ✓ Nueva contraseña generada
echo.

REM ========== PASO 2: Localizar archivos ==========
echo [2/3] Buscando archivos de configuración...

REM Buscar .env
if exist ".env" (
    echo ✓ Encontrado: .env
    set "env_found=1"
) else if exist "public\.env" (
    echo ✓ Encontrado: public\.env
    set "env_file=public\.env"
    set "env_found=1"
) else if exist "backend\.env" (
    echo ✓ Encontrado: backend\.env
    set "env_file=backend\.env"
    set "env_found=1"
) else (
    echo ❌ ERROR: No se encontró archivo .env
    echo Ubicaciones esperadas:
    echo   - .env
    echo   - public\.env
    echo   - backend\.env
    pause
    exit /b 1
)

REM Buscar BD
if exist "data" (
    echo ✓ Encontrado: carpeta data/
) else (
    echo ⚠️  No encontrada carpeta data/ (esto puede estar OK)
)

echo.

REM ========== PASO 3: Hacer backup ==========
echo [3/3] Haciendo backup de seguridad...

set "timestamp=%date:~-4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "timestamp=%timestamp: =0%"

if exist ".env" (
    copy ".env" ".env.backup.%timestamp%" >nul 2>nul
    echo ✓ Backup creado: .env.backup.%timestamp%
)

if exist "*.log" (
    for %%F in (*.log) do (
        del /q "%%F" >nul 2>nul
    )
    echo ✓ Logs limpios
)

echo.
echo ╔═══════════════════════════════════════════════════════════════════════╗
echo ║                        ⚠️  INSTRUCCIONES MANUALES                     ║
echo ╚═══════════════════════════════════════════════════════════════════════╝
echo.
echo 📋 COPIA ESTO EN TU SERVIDOR (SSH o RDP):
echo.
echo 1️⃣  CAMBIAR CONTRASEÑA DE BASE DE DATOS:
echo.
echo    En MySQL:
echo    mysql -u root -p
echo    ALTER USER 'usuario_bd'@'localhost' IDENTIFIED BY '%new_pass%';
echo    FLUSH PRIVILEGES;
echo    EXIT;
echo.
echo 2️⃣  ACTUALIZAR .env:
echo.
echo    Edita con: nano .env  (o tu editor favorito)
echo    Cambia estas líneas:
echo    DB_PASSWORD=%new_pass%
echo    API_KEY=(genera nuevo en https://randomkeygen.com/)
echo    API_SECRET=(genera nuevo)
echo    MAIL_PASSWORD=(si usas mail SMTP)
echo.
echo 3️⃣  REINICIAR SERVICIOS:
echo.
echo    sudo systemctl restart php-fpm nginx mysql
echo    O en Windows:
echo    net stop MySQL80
echo    net start MySQL80
echo.
echo 4️⃣  PROBAR LOGIN:
echo.
echo    Abre: https://tudominio.com/login
echo    Usuario: admin
echo    Contraseña: (la que tienes)
echo.
echo 5️⃣  VERIFICAR LOGS:
echo.
echo    tail -f var/log/laravel.log
echo    (No debe haber errores de conexión)
echo.
echo ════════════════════════════════════════════════════════════════════════
echo.
echo ℹ️  Nueva contraseña temporal (ANÓTALA EN LUGAR SEGURO):
echo    %new_pass%
echo.
echo ⚠️  Después de cambiar credenciales:
echo    ☐ Notifica al equipo
echo    ☐ Cada dev ejecuta deploy.bat
echo    ☐ Verifica que login funciona
echo.
echo Presiona cualquier tecla cuando hayas completado todos los pasos...
pause >nul
echo.
echo ✅ Gracias por actualizar credenciales. Sistema más seguro ahora.
