#!/bin/bash

# ==============================================================================
# DFAR Integrated Ecosystem - One-Click Launcher (MSDFAR Main + HEALTH Subdomain)
# ==============================================================================

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ -d "$SCRIPT_DIR/MSDFAR/dfar_ms" ]; then
    ROOT_DIR="$SCRIPT_DIR"
elif [ -d "$SCRIPT_DIR/HEALTH-MSDFAR/Untitled/MSDFAR/dfar_ms" ]; then
    ROOT_DIR="$SCRIPT_DIR/HEALTH-MSDFAR/Untitled"
elif [ -d "$SCRIPT_DIR/Untitled/MSDFAR/dfar_ms" ]; then
    ROOT_DIR="$SCRIPT_DIR/Untitled"
elif [ -d "$HOME/Desktop/HEALTH-MSDFAR/Untitled/MSDFAR/dfar_ms" ]; then
    ROOT_DIR="$HOME/Desktop/HEALTH-MSDFAR/Untitled"
elif [ -d "/Users/mesandasethumika/Desktop/HEALTH-MSDFAR/Untitled/MSDFAR/dfar_ms" ]; then
    ROOT_DIR="/Users/mesandasethumika/Desktop/HEALTH-MSDFAR/Untitled"
else
    FOUND_PATH=$(find "$HOME/Desktop" "$HOME" -maxdepth 4 -name "dfar_ms" 2>/dev/null | head -n 1)
    if [ -n "$FOUND_PATH" ]; then
        ROOT_DIR="$(dirname "$(dirname "$FOUND_PATH")")"
    else
        echo "❌ Error: Could not locate MSDFAR project directory."
        exit 1
    fi
fi

cd "$ROOT_DIR"

echo "=================================================================="
echo "  🌟 Starting DFAR Integrated Ecosystem (MSDFAR + HEALTH SSO)"
echo "  📁 Working Directory: $ROOT_DIR"
echo "=================================================================="

# 1. Clean up old processes on all ports
echo "🧹 [1/4] Freeing all project ports (8080, 8081, 7239, 5064, 57549)..."
lsof -ti:8080 | xargs kill -9 2>/dev/null
lsof -ti:8081 | xargs kill -9 2>/dev/null
lsof -ti:7239 | xargs kill -9 2>/dev/null
lsof -ti:5064 | xargs kill -9 2>/dev/null
lsof -ti:57549 | xargs kill -9 2>/dev/null
pkill -f "MEA.Server" 2>/dev/null
pkill -f "php -S" 2>/dev/null
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
    sleep 2
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

# 3. Launch MSDFAR (Main Portal - listening on 0.0.0.0 to support both localhost and 127.0.0.1 in Safari)
echo "🏛️  [3/4] Launching MSDFAR Main System..."
osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/MSDFAR/dfar_ms\" && echo '=== [MSDFAR] Starting Backend (http://localhost:8080) ===' && php -S 0.0.0.0:8080 -t backend/web backend/web/router.php"
end tell
EOF

osascript <<EOF
tell application "Terminal"
    do script "cd \"$ROOT_DIR/MSDFAR/dfar_ms\" && echo '=== [MSDFAR] Starting Frontend (http://localhost:8081) ===' && php -S 0.0.0.0:8081 -t frontend/web frontend/web/router.php"
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

echo "⏳ Waiting for all services to finish initializing..."

# Check MSDFAR
echo "  [1/3] Checking MSDFAR..."
for i in {1..20}; do
    if curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8080/login" 2>/dev/null | grep -q "200\|302"; then
        echo "  ✅ MSDFAR Main Portal is ready."
        break
    fi
    sleep 1
done

# Check HEALTH API
echo "  [2/3] Checking HEALTH .NET API..."
for i in {1..30}; do
    if curl -k -s -o /dev/null -w "%{http_code}" "https://127.0.0.1:7239/swagger/index.html" 2>/dev/null | grep -q "200\|302\|404"; then
        echo "  ✅ HEALTH .NET Backend API is ready."
        break
    fi
    sleep 1
done

# Check HEALTH Angular
echo "  [3/3] Compiling and loading HEALTH Angular UI..."
for i in {1..45}; do
    if curl -k -s -o /dev/null -w "%{http_code}" "https://127.0.0.1:57549/" 2>/dev/null | grep -q "200\|304"; then
        echo "  ✅ HEALTH Angular Client is ready!"
        break
    fi
    printf "."
    sleep 1
done
echo ""

# Open FIRST and ONLY the MSDFAR login page in default browser
sleep 1
echo "🖥️  Opening MSDFAR Main Login page in Safari/Browser..."
open "http://localhost:8080/login"

echo "=================================================================="
echo "  🎉 Ecosystem is now fully running!"
echo "  - MSDFAR Main Login:     http://localhost:8080/login"
echo "  - MSDFAR Main Frontend:  http://localhost:8081"
echo "  - HEALTH Angular Portal: https://localhost:57549"
echo "  - HEALTH .NET API:       https://localhost:7239"
echo "  - HEALTH Swagger Docs:   https://localhost:7239/swagger"
echo "=================================================================="
