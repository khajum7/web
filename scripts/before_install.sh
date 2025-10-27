#!/bin/bash

set -e

echo "Starting before_install hook..."

# Install system dependencies if not already installed
echo "Installing system dependencies..."
apt-get update -y
apt-get install -y postgresql postgresql-contrib redis-server apache2 nodejs npm build-essential python3

# Install pnpm if not already installed
if ! command -v pnpm &> /dev/null; then
    echo "Installing pnpm..."
    npm install -g pnpm@8.15.5
fi

# Setup PostgreSQL if not already configured
echo "Setting up PostgreSQL..."
systemctl enable postgresql
systemctl start postgresql

# Create database and user if they don't exist
sudo -u postgres psql -c "CREATE USER \"Postman-user\" WITH PASSWORD 'PqwriuofhadgfiaduhUIFvb#iudf234';" 2>/dev/null || echo "User Postman-user already exists"
sudo -u postgres psql -c "ALTER USER \"Postman-user\" WITH SUPERUSER;" 2>/dev/null || echo "User Postman-user already has superuser privileges"
sudo -u postgres psql -c "CREATE DATABASE hoppdb OWNER \"Postman-user\";" 2>/dev/null || echo "Database hoppdb already exists"

# Enable and start Redis
systemctl enable redis-server
systemctl start redis-server

# Enable and start Apache
systemctl enable apache2

# Stop services before deployment
echo "Stopping services before deployment..."

# Stop backend if running
systemctl stop hoppscotch-backend 2>/dev/null || true

# Stop Apache to avoid conflicts
systemctl stop apache2 2>/dev/null || true

echo "Before install completed successfully"
