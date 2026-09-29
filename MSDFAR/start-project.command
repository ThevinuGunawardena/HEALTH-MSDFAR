#!/bin/bash

# ==============================================================================
# DFAR Main System - One-Click Launcher for macOS
# ==============================================================================

PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$PROJECT_DIR/dfar_ms" 2>/dev/null || cd "$PROJECT_DIR"

echo "=================================================================="
echo "  🚀 Starting DFAR Management System (MSDFAR)"
echo "=================================================================="

# 1. Clean up old processes holding ports 8080, 8081
echo "🧹 [1/3] Checking and clearing ports (8080, 8081)..."
lsof -ti:8080 | xargs kill -9 2>/dev/null
lsof -ti:8081 | xargs kill -9 2>/dev/null
sleep 1
echo "✅ Ports are clear."

# 2. Check MySQL
echo "🐬 [2/3] Checking MySQL connection..."
if nc -z 127.0.0.1 3306 2>/dev/null; then
    echo "✅ MySQL server is running on port 3306."
else
    echo "⚠️ MySQL not detected on port 3306. Attempting to start MySQL..."
    mysql.server start 2>/dev/null || brew services start mysql 2>/dev/null || true
    sleep 2
fi

# 3. Launch Backend (port 8080) and Frontend (port 8081)
echo "⚡ [3/3] Launching MSDFAR Backend & Frontend servers..."

osascript <<EOF
tell application "Terminal"
    do script "cd \"$PROJECT_DIR/dfar_ms\" && echo '=== Starting MSDFAR Backend (http://localhost:8080) ===' && php -S 127.0.0.1:8080 -t backend/web backend/web/router.php"
end tell
EOF

osascript <<EOF
tell application "Terminal"
    do script "cd \"$PROJECT_DIR/dfar_ms\" && echo '=== Starting MSDFAR Frontend (http://localhost:8081) ===' && php -S 127.0.0.1:8081 -t frontend/web frontend/web/router.php"
end tell
EOF

echo "⏳ Waiting for MSDFAR servers to start..."
for i in {1..15}; do
    if nc -z 127.0.0.1 8080 2>/dev/null; then
        echo "✅ MSDFAR Backend is ready at http://localhost:8080"
        break
    fi
    sleep 1
done

sleep 1
echo "🖥️  Opening MSDFAR Backend in browser..."
open "http://localhost:8080"

echo "=================================================================="
echo "  🎉 MSDFAR System is up and running!"
echo "  - Backend Admin Portal: http://localhost:8080"
echo "  - Frontend Portal:      http://localhost:8081"
echo "=================================================================="
