#!/bin/bash

# ==============================================================================
# DFAR Main System - Stop Script for macOS
# ==============================================================================

echo "🛑 Stopping MSDFAR servers (ports 8080, 8081)..."
lsof -ti:8080 | xargs kill -9 2>/dev/null
lsof -ti:8081 | xargs kill -9 2>/dev/null
sleep 1
echo "✅ MSDFAR servers have been stopped."
