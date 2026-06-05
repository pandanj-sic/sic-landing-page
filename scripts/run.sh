#!/bin/bash

echo "Starting production post-deployment scripts..."

# Optimize Laravel for production
echo "Discovering packages and caching configurations..."
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link public storage directory
echo "Creating storage symlink..."
mkdir -p /var/www/html/storage/app/public
php artisan storage:link --force

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Post-deployment scripts completed!"
