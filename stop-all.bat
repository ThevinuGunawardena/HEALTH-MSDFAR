@echo off
title Stop DFAR Ecosystem

echo Stopping all DFAR and HEALTH services...
taskkill /F /FI "WINDOWTITLE eq MSDFAR-Backend*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq MSDFAR-Frontend*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq HEALTH-API*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq HEALTH-Client*" >nul 2>&1
taskkill /F /IM "MEA.Server.exe" >nul 2>&1
taskkill /F /IM "php.exe" >nul 2>&1

echo All DFAR services have been stopped.
pause
