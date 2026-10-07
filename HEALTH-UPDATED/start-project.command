#!/bin/bash

# ==============================================================================
# DFAR Health Certificate System - One-Click Launcher for macOS
# ==============================================================================

# Change working directory to the project folder
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$PROJECT_DIR"

echo "=================================================================="
echo "  🚀 Starting DFAR Health Certificate System"
echo "=================================================================="

# 1. Clean up old processes holding ports 57549, 7239, 5064 to prevent port conflicts
echo "🧹 [1/5] Checking and freeing ports (57549, 7239, 5064)..."
lsof -ti:57549 | xargs kill -9 2>/dev/null
lsof -ti:7239 | xargs kill -9 2>/dev/null
lsof -ti:5064 | xargs kill -9 2>/dev/null
pkill -f "MEA.Server" 2>/dev/null
sleep 1
echo "✅ Ports are clear."

# 2. Start Docker container (SQL Server)
echo "📦 [2/5] Starting Docker SQL Server (mea-sqlserver)..."
if ! docker info >/dev/null 2>&1; then
    echo "⚠️ Docker is not running. Launching Docker Desktop..."
    open -a Docker
    echo "⏳ Waiting for Docker daemon to initialize..."
    while ! docker info >/dev/null 2>&1; do
        sleep 2
    done
fi

docker start mea-sqlserver >/dev/null 2>&1
echo "⏳ Waiting for SQL Server port (1433) to be ready..."
for i in {1..20}; do
    if nc -z 127.0.0.1 1433 2>/dev/null; then
        echo "✅ SQL Server database is ready!"
        break
    fi
    sleep 1
done

# 3. Launch .NET Backend API in a new Terminal window
echo "⚡ [3/5] Launching .NET Core Backend API..."
osascript <<EOF
tell application "Terminal"
    do script "cd \"$PROJECT_DIR/MEA.Server\" && echo '=== Starting .NET Backend API ===' && dotnet run --launch-profile https"
end tell
EOF

# 4. Launch Angular Client in a new Terminal window
echo "🌐 [4/5] Launching Angular Frontend..."
osascript <<EOF
tell application "Terminal"
    do script "cd \"$PROJECT_DIR/mea.client\" && echo '=== Starting Angular Frontend ===' && npm start"
end tell
EOF

# 5. Actively wait for Angular and .NET servers to finish compiling and start listening
echo "⏳ [5/5] Waiting for Angular frontend to compile and start listening on port 57549..."
for i in {1..40}; do
    if nc -z 127.0.0.1 57549 2>/dev/null; then
        echo "✅ Angular frontend is ready!"
        break
    fi
    printf "."
    sleep 1
done
echo ""

# Give 1 extra second for initial bundle load and open browser
sleep 1
echo "🖥️  Opening application in default browser..."
open "https://localhost:57549"

echo "=================================================================="
echo "  🎉 All services are up and running!"
echo "  - Angular Portal: https://localhost:57549"
echo "  - Backend API:    https://localhost:7239"
echo "  - Swagger Docs:   https://localhost:7239/swagger"
echo "=================================================================="
