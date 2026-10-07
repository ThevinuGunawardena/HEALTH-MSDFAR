@echo off
setlocal EnableDelayedExpansion
title DFAR Integrated Ecosystem (MSDFAR + HEALTH)

echo ==================================================================
echo   Starting DFAR Integrated Ecosystem (MSDFAR + HEALTH)
echo ==================================================================

set "SCRIPT_DIR=%~dp0"
if exist "%SCRIPT_DIR%MSDFAR\dfar_ms" (
    set "ROOT_DIR=%SCRIPT_DIR%"
) else if exist "%SCRIPT_DIR%HEALTH-MSDFAR\Untitled\MSDFAR\dfar_ms" (
    set "ROOT_DIR=%SCRIPT_DIR%HEALTH-MSDFAR\Untitled\"
) else if exist "%SCRIPT_DIR%Untitled\MSDFAR\dfar_ms" (
    set "ROOT_DIR=%SCRIPT_DIR%Untitled\"
) else if exist "%USERPROFILE%\Desktop\HEALTH-MSDFAR\Untitled\MSDFAR\dfar_ms" (
    set "ROOT_DIR=%USERPROFILE%\Desktop\HEALTH-MSDFAR\Untitled\"
) else (
    set "ROOT_DIR=%SCRIPT_DIR%"
)

cd /d "!ROOT_DIR!"

:: 1. Check / Start MySQL
echo Checking MySQL service...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running.
) else (
    echo [..] Starting MySQL from XAMPP...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 3 >nul
        echo [OK] MySQL started.
    )
)

:: 2. Check / Start Docker SQL Server
echo Checking Docker SQL Server...
docker start mea-sqlserver >nul 2>&1

:: 3. Start MSDFAR Backend & Frontend
set "PHP_BIN=php"
if exist "C:\Users\Administrator\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
    set "PHP_BIN=C:\Users\Administrator\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
)

echo Starting MSDFAR Backend on http://localhost:8080 ...
start "MSDFAR-Backend" /min cmd /c "cd /d "!ROOT_DIR!MSDFAR\dfar_ms" && "!PHP_BIN!" -d max_execution_time=600 -d memory_limit=1024M -S 0.0.0.0:8080 -t backend/web backend/web/router.php"

echo Starting MSDFAR Frontend on http://localhost:8081 ...
start "MSDFAR-Frontend" /min cmd /c "cd /d "!ROOT_DIR!MSDFAR\dfar_ms" && "!PHP_BIN!" -d max_execution_time=600 -d memory_limit=1024M -S 0.0.0.0:8081 -t frontend/web frontend/web/router.php"

:: 4. Start HEALTH .NET API & Angular Client
if exist "!ROOT_DIR!Health Certificate - DFAR\MEA.Server" (
    set "HEALTH_DIR=!ROOT_DIR!Health Certificate - DFAR"
) else (
    set "HEALTH_DIR=!ROOT_DIR!HEALTH"
)

echo Starting HEALTH .NET Backend API...
start "HEALTH-API" /min cmd /c "cd /d "!HEALTH_DIR!\MEA.Server" && dotnet run --launch-profile https"

echo Starting HEALTH Angular Client...
start "HEALTH-Client" /min cmd /c "cd /d "!HEALTH_DIR!\mea.client" && npm start"

timeout /t 5 >nul

:: 5. Open ONLY the MSDFAR login page in browser
echo Opening MSDFAR Login page in browser...
start http://localhost:8080/login

echo ==================================================================
echo   DFAR Application is now running!
echo   - MSDFAR Main Login: http://localhost:8080/login
echo ==================================================================
pause
