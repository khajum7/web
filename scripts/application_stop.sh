#!/bin/bash

echo "Stopping application..."

# Stop backend service
systemctl stop hoppscotch-backend 2>/dev/null || true

# Stop Apache
systemctl stop apache2 2>/dev/null || true

echo "Application stopped successfully"
