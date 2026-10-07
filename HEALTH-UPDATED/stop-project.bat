@echo off
title Stopping DFAR Health Certificate System...
echo ==================================================================
echo   Stopping DFAR Health Certificate System Services
echo ==================================================================

echo Stopping .NET Server...
taskkill /F /IM "MEA.Server.exe" >nul 2>&1
taskkill /F /IM "dotnet.exe" >nul 2>&1

echo Stopping Angular Node server...
taskkill /F /IM "node.exe" >nul 2>&1

echo Stopping Docker SQL Server container...
docker stop mea-sqlserver >nul 2>&1

echo ==================================================================
echo   All DFAR services have been stopped.
echo ==================================================================
pause
