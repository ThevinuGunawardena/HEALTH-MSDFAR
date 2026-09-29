@echo off
title DFAR Stopper
echo Stopping PHP built-in web servers on ports 8080 and 8081...

for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8080 " ^| findstr "LISTENING"') do (
    echo Stopping process on port 8080 (PID %%a)...
    taskkill /F /PID %%a 2>nul
)

for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8081 " ^| findstr "LISTENING"') do (
    echo Stopping process on port 8081 (PID %%a)...
    taskkill /F /PID %%a 2>nul
)

echo Done.
pause
