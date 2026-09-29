#!/bin/bash

# ==============================================================================
# DFAR Integrated Ecosystem - Stop All Services
# ==============================================================================

echo "🛑 Stopping all MSDFAR and HEALTH services..."
lsof -ti:8080 | xargs kill -9 2>/dev/null
lsof -ti:8081 | xargs kill -9 2>/dev/null
lsof -ti:7239 | xargs kill -9 2>/dev/null
lsof -ti:5064 | xargs kill -9 2>/dev/null
lsof -ti:57549 | xargs kill -9 2>/dev/null
pkill -f "MEA.Server" 2>/dev/null
pkill -f "php -S 127.0.0.1:8080" 2>/dev/null
pkill -f "php -S 127.0.0.1:8081" 2>/dev/null
sleep 1
echo "✅ All DFAR services have been stopped."
