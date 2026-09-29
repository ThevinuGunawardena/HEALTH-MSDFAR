@echo off
setlocal EnableDelayedExpansion
title DFAR Local Runner

echo ========================================================
echo           Starting DFAR Application Locally
echo ========================================================

:: 1. Ensure working directory is dfar_ms
cd /d "%~dp0"
if exist "dfar_ms" cd dfar_ms

:: 2. Set PHP 8.2 Binary
set "PHP_BIN=C:\Users\Administrator\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
if not exist "!PHP_BIN!" (
    set "PHP_BIN=php"
)

:: 3. Check / Start MySQL
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
    ) else (
        echo [ERROR] MySQL executable not found in C:\xampp\mysql\bin\
    )
)

:: 4. Start Backend Server on Port 8080
echo Starting Backend on http://localhost:8080 ...
start "DFAR-Backend" /min cmd /c ""!PHP_BIN!" -S 127.0.0.1:8080 -t backend/web backend/web/router.php"

:: 5. Start Frontend Server on Port 8081
echo Starting Frontend on http://localhost:8081 ...
start "DFAR-Frontend" /min cmd /c ""!PHP_BIN!" -S 127.0.0.1:8081 -t frontend/web frontend/web/router.php"

timeout /t 2 >nul

echo.
echo ========================================================
echo  DFAR Application is now running!
echo  - Backend:  http://localhost:8080
echo  - Frontend: http://localhost:8081
echo ========================================================
echo.
echo Opening Backend in browser...
start http://localhost:8080

pause
