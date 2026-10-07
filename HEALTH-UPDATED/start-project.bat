@echo off
title Starting DFAR Health Certificate System...
echo ==================================================================
echo   Starting DFAR Health Certificate System
echo ==================================================================

:: Change directory to where the batch script is located
cd /d "%~dp0"

echo [1/5] Freeing previous port processes if any...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :57549') do taskkill /F /PID %%a >nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :7239') do taskkill /F /PID %%a >nul 2>&1

echo [2/5] Starting Docker SQL Server (mea-sqlserver)...
docker start mea-sqlserver >nul 2>&1
if %errorlevel% neq 0 (
    echo Docker container 'mea-sqlserver' not running. Attempting to start...
    docker start mea-sqlserver
)

echo [3/5] Launching .NET Backend API...
start "DFAR .NET Backend API" cmd /k "cd /d "%~dp0MEA.Server" && dotnet run --launch-profile https"

echo [4/5] Launching Angular Frontend...
start "DFAR Angular Frontend" cmd /k "cd /d "%~dp0mea.client" && npm start"

echo [5/5] Opening Web Application in Browser...
timeout /t 5 >nul
start https://localhost:57549/

echo ==================================================================
echo   All services launched in separate windows!
echo   - Backend API:    https://localhost:7239
echo   - Swagger Docs:   https://localhost:7239/swagger
echo   - Angular Portal: https://localhost:57549
echo ==================================================================
pause
