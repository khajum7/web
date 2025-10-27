#!/bin/bash

set -e

echo "Starting after_install hook..."

# Set environment variables
export DATABASE_URL="postgresql://Postman-user:PqwriuofhadgfiaduhUIFvb%23iudf234@13.52.23.186:5432/hoppdb?schema=public"
export PRODUCTION="true"
export PORT="3170"
export TRUST_PROXY="true"
export WHITELISTED_ORIGINS="http://man.shikhartech.com,https://man.shikhartech.com"

# Navigate to project directory
cd /var/www/html/hoppscotch

# Install dependencies
echo "Installing dependencies..."
pnpm install --no-frozen-lockfile

# Setup backend
echo "Setting up backend..."
cd packages/hoppscotch-backend

# Generate Prisma client and run migrations
echo "Generating Prisma client..."
pnpm exec prisma generate

echo "Running database migrations..."
pnpm exec prisma migrate deploy

# Build backend
echo "Building backend..."
pnpm run build

cd /var/www/html/hoppscotch

echo "After install completed successfully"
