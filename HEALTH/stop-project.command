#!/bin/bash

# ==============================================================================
# DFAR Health Certificate System - One-Click Stopper for macOS
# ==============================================================================

echo "=================================================================="
echo "  🛑 Stopping DFAR Health Certificate System Services"
echo "=================================================================="

# 1. Stop .NET server process
echo "Stopping .NET Server..."
pkill -f "MEA.Server" 2>/dev/null

# 2. Stop Angular node/ng process
echo "Stopping Angular Frontend..."
pkill -f "ng serve" 2>/dev/null

# 3. Stop Docker SQL Server container
echo "Stopping Docker container (mea-sqlserver)..."
docker stop mea-sqlserver >/dev/null 2>&1

echo "=================================================================="
echo "  ✅ All DFAR services have been stopped."
echo "=================================================================="
