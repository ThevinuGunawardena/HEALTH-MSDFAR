#!/bin/bash

# ==============================================================================
# DFAR Integrated Ecosystem - One-Click Launcher (MSDFAR Main + HEALTH Subdomain)
# ==============================================================================

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$ROOT_DIR"

echo "=================================================================="
echo "  🌟 Starting DFAR Integrated Ecosystem (MSDFAR + HEALTH SSO)"
echo "=================================================================="

# 1. Clean up old processes on all ports
echo "🧹 [1/4] Freeing all project ports (8080, 8081, 7239, 5064, 57549)..."
lsof -ti:8080 | xargs kill -9 2>/dev/null
lsof -ti:8081 | xargs kill -9 2>/dev/null
lsof -ti:7239 | xargs kill -9 2>/dev/null
lsof -ti:5064 | xargs kill -9 2>/dev/null
lsof -ti:57549 | xargs kill -9 2>/dev/null
pkill -f "MEA.Server" 2>/dev/null
sleep 1
echo "✅ Ports are clear."

# 2. Check & Start Databases
echo "📦 [2/4] Checking databases (MySQL & Docker SQL Server)..."

# MySQL
if nc -z 127.0.0.1 3306 2>/dev/null; then
    echo "  ✅ MySQL is running on port 3306."
else
    echo "  ⚠️ Starting MySQL service..."
    mysql.server start 2>/dev/null || brew services start mysql 2>/dev/null || true
fi

# Docker SQL Server
if ! docker info >/dev/null 2>&1; then
    echo "  ⚠️ Starting Docker Desktop..."
    open -a Docker
    while ! docker info >/dev/null 2>&1; do
        sleep 2
    done
fi
docker start mea-sqlserver >/dev/null 2>&1
for i in {1..20}; do
    if nc -z 127.0.0.1 1433 2>/dev/null; then
        echo "  ✅ SQL Server (mea-sqlserver) is ready on port 1433."
        break
    fi
    sleep 1
done

# 3. Launch MSDFAR (Main Portal)
echo "🏛️  [3/4] Launching MSDFAR Main System..."
osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/MSDFAR/dfar_ms\" && echo '=== [MSDFAR] Starting Backend (http://localhost:8080) ===' && php -S 127.0.0.1:8080 -t backend/web backend/web/router.php"
end tell
EOF

osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/MSDFAR/dfar_ms\" && echo '=== [MSDFAR] Starting Frontend (http://localhost:8081) ===' && php -S 127.0.0.1:8081 -t frontend/web frontend/web/router.php"
end tell
EOF

# 4. Launch HEALTH (.NET Backend API + Angular Client)
echo "🩺 [4/4] Launching HEALTH Certificate Subdomain..."
osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/HEALTH/MEA.Server\" && echo '=== [HEALTH] Starting .NET Backend API (https://localhost:7239) ===' && dotnet run --launch-profile https"
end tell
EOF

osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/HEALTH/mea.client\" && echo '=== [HEALTH] Starting Angular Frontend (https://localhost:57549) ===' && npm start"
end tell
EOF

echo "⏳ Waiting for services to initialize..."
for i in {1..40}; do
    if nc -z 127.0.0.1 57549 2>/dev/null && nc -z 127.0.0.1 8080 2>/dev/null; then
        echo "✅ All services are active and listening!"
        break
    fi
    printf "."
    sleep 1
done
echo ""

sleep 1
echo "🖥️  Opening portals in browser..."
open "http://localhost:8080"
open "https://localhost:57549"

echo "=================================================================="
echo "  🎉 Ecosystem is now fully running!"
echo "  - MSDFAR Main Backend: http://localhost:8080"
echo "  - MSDFAR Main Frontend: http://localhost:8081"
echo "  - HEALTH Angular Portal: https://localhost:57549"
echo "  - HEALTH .NET API:       https://localhost:7239"
echo "  - HEALTH Swagger Docs:   https://localhost:7239/swagger"
echo "=================================================================="
